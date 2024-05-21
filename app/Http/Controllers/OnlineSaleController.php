<?php

namespace App\Http\Controllers;

use App\Models\GlobalProductStore;
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

        // return $products;
        return inertia('OnlineSale/ClientIndex', compact('store', 'products', 'total_products'));
    }


    public function cartIndex()
    {   
        return inertia('OnlineSale/Cart');
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
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
}
