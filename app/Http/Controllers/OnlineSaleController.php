<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\GlobalProductStore;
use App\Models\Logo;
use App\Models\OnlineSale;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;

class OnlineSaleController extends Controller
{
    
    public function index()
    {   
        //
    }


    public function clientIndex($store_id)
    {   
        $store = Store::find($store_id);
        $all_products = $this->getAllProducts($store_id); //locales y transferidos 
        $total_products = $all_products->count(); //Número de productos locales y transferidos 

        //tomar solo primeros 12 productos
        $products = $all_products->take(12);

        $banners = Banner::with(['media'])->where('store_id', $store_id)->first();

        // return $banners;
        return inertia('OnlineSale/ClientIndex', compact('store', 'products', 'total_products', 'store_id', 'banners'));
    }


    public function cartIndex()
    {   
        return inertia('OnlineSale/Cart');
    }

    
    public function create()
    {
        return inertia('OnlineSale/Create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:10',
            'email' => 'nullable|email',
            'suburb' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'ext_number' => 'required|string|min:1|max:50',
            'int_number' => 'nullable|string|min:1|max:50',
        ]);

        OnlineSale::create($request->all());
    }

    
    public function show(OnlineSale $online_sale)
    {
        //
    }


    public function ShowLocalProduct($product_id)
    {
        $product = Product::with(['media', 'category:id,name', 'brand:id,name'])->find($product_id);

        return inertia('OnlineSale/ShowLocalProduct', compact('product'));
    }


    public function ShowGlobalProduct($global_product_id)
    {   
        $global_product = GlobalProductStore::with(['globalProduct' => ['media', 'category:id,name', 'brand:id,name']])->find($global_product_id);

        return inertia('OnlineSale/ShowGlobalProduct', compact('global_product'));
    }

    
    public function edit(OnlineSale $online_sale)
    {
        //
    }

    
    public function update(Request $request, OnlineSale $online_sale)
    {
        //
    }

    
    public function destroy(OnlineSale $online_sale)
    {
        //
    }


    public function getAllProducts($store_id)
    {
        // productos creados localmente en la tienda que no están en el catálogo base o global
        $local_products = Product::with(['category:id,name', 'brand:id,name', 'media'])
            ->where('store_id', $store_id)
            ->latest('id')
            ->get(['id', 'name', 'public_price', 'code', 'store_id', 'category_id', 'brand_id', 'min_stock', 'max_stock', 'current_stock']);
            
            // productos transferidos desde el catálogo base
            $transfered_products = GlobalProductStore::with(['globalProduct' => ['media', 'category']])->where('store_id', $store_id)->get();

        // Creamos un nuevo arreglo combinando los dos conjuntos de datos
        $merged = array_merge($local_products->toArray(), $transfered_products->toArray());
        $products = collect($merged);

        return $products;
    }


    public function loadMoreProducts($offset, $limit)
    {
        // Cargar más productos desde la base de datos tomando sólo los requeridos y saltando los ya cargados
        $all_products = $this->getAllProducts(request('storeId'));
        $moreProducts = $all_products->splice($offset)->take($limit);

        return response()->json(['products' => $moreProducts]);
    }


    public function fetchProduct($product_id, $is_local)
    {
       if ( $is_local === 'true' ) {
            $product = Product::with(['media'])->find($product_id);
       } else {
            $product = GlobalProductStore::with(['globalProduct.media'])->find($product_id);
       }

       return response()->json(['item' => $product]);
    }


    public function searchProducts(Request $request, $store_id)
    {
        $query = $request->input('query');

        // Realiza la búsqueda en la base de datos local
        $local_products = Product::with(['category', 'brand', 'media'])
            ->where('store_id', $store_id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('code', 'like', "%$query%");
            })
            ->get();

        $global_products = GlobalProductStore::with(['globalProduct.media'])
            ->whereHas('globalProduct', function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', "%$query%")
                    ->orWhere('code', $query);
            })
            ->where('store_id', $store_id)
            ->get();

        // Combinar los resultados en una colección
        $combined_products = $local_products->merge($global_products);

        // Tomar solo los primeros 20 elementos del arreglo combinado
        $products = $combined_products;

        return response()->json(['items' => $products]);
    }


    public function getLogo($store_id)
    {
        $logo = Logo::with(['media'])->where('store_id', $store_id)->first();

        return response()->json(['item' => $logo]);
    }
}
