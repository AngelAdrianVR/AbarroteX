<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\CashRegister;
use App\Models\GlobalProductStore;
use App\Models\Logo;
use App\Models\OnlineSale;
use App\Models\Product;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OnlineSaleController extends Controller
{
    
    public function index()
    {   
        $banners = Banner::with(['media'])->where('store_id', auth()->user()->store_id)->first();
        $logo = Logo::with(['media'])->where('store_id', auth()->user()->store_id)->first();
        $cash_registers = CashRegister::where('store_id', auth()->user()->store_id)->get();
        $online_orders = OnlineSale::where('store_id', auth()->user()->store_id)->latest()->get()->take(20);
        $total_online_orders = OnlineSale::where('store_id', auth()->user()->store_id)->get()->count();

        // return $cash_registers;
        return inertia('OnlineSale/Index', compact('banners', 'logo', 'online_orders', 'cash_registers', 'total_online_orders'));
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
            'address_references' => 'nullable|string|min:1|max:255',
            'products' => 'required|array|min:1',
        ]);

        //descontar de inventario la cantidad solicitada si la configuración de inventario está activa
        // Pendiente el codigo


        $new_online_sale = OnlineSale::create($request->all());

        if ( $request->created_from_app === true ) {
            return to_route('online-sales.show', $new_online_sale->id );
        } else {
            return to_route('online-sales.client-index', ['store_id' => $request->store_id]);
        }
    }

    
    public function show(OnlineSale $online_sale)
    {   
        return inertia('OnlineSale/Show', compact('online_sale'));
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
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:10',
            'email' => 'nullable|email',
            'suburb' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'ext_number' => 'required|string|min:1|max:50',
            'int_number' => 'nullable|string|min:1|max:50',
            'address_references' => 'nullable|string|min:1|max:255',
            'products' => 'required|array|min:1',
        ]);

        $online_sale->update($request->all());

        return to_route('online-sales.show', $online_sale->id );
    }

    
    public function destroy(OnlineSale $online_sale)
    {
        $online_sale->delete();
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


    public function filterOnlineSales(Request $request)
    {
        $queryDate = $request->input('queryDate');
        $startDate = Carbon::parse($queryDate[0])->startOfDay();
        $endDate = Carbon::parse($queryDate[1])->endOfDay();

        // Obtener los gastos registrados en el rango de fechas requerido por el filtro
        $online_orders = OnlineSale::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->latest()->get();

        return response()->json(['items' => $online_orders]);
    }


    public function updateOnlineSaleStatus(Request $request, OnlineSale $online_sale)
    {
        // Diccionario de estados válidos y sus traducciones
        $status_map = [
            'pendent' => 'Pendiente',
            'processing' => 'Procesando',
            'delivered' => 'Entregado',
            'cancel' => 'Cancelado'
        ];

        // Verifica si el estado solicitado es válido
        if (!isset($status_map[$request->status])) {
            return response()->json(['error' => 'Estado inválido'], 400);
        }

        $status = $status_map[$request->status];
        $delivered_at = null;

        // Si se cambia desde 'delivered' a otro estado y existe una caja configurada
        if ($online_sale->delivered_at && $request->online_sales_cash_register) {
            $total_sale = $online_sale->total + $online_sale->delivery_price;
            $cash_register = CashRegister::find($request->online_sales_cash_register);
            $cash_register->current_cash -= $total_sale;
            $cash_register->save();
        }

        // Si se cambia el estado a 'delivered' y existe una caja configurada
        if ($request->status == 'delivered' && $request->online_sales_cash_register) {
            $total_sale = $online_sale->total + $online_sale->delivery_price;
            $cash_register = CashRegister::find($request->online_sales_cash_register);
            $cash_register->current_cash += $total_sale;
            $cash_register->save();
            $delivered_at = now();
        }

        $online_sale->update([
            'status' => $status,
            'delivered_at' => $delivered_at,
        ]);

        return response()->json(compact('status', 'delivered_at'));
    }



    public function fetchAllProducts()
    {
        // Productos creados localmente en la tienda que no están en el catálogo base o global
        $local_products = Product::with('media')->where('store_id', auth()->user()->store_id)
            ->latest('id')
            ->get(['id', 'name', 'public_price', 'current_stock']);

        // Productos transferidos desde el catálogo base
        $transfered_products = GlobalProductStore::with('globalProduct.media', 'globalProduct:id,name,public_price')
            ->where('store_id', auth()->user()->store_id)
            ->get();

        // Creamos un nuevo arreglo combinando los dos conjuntos de datos
        $merged = array_merge($local_products->toArray(), $transfered_products->toArray());
        
        // Inicializamos el contador para relative_id
        $relative_id = 1;

        // Construimos un nuevo arreglo con el formato especificado
        $products = array_map(function($product) use (&$relative_id) {
            // Si el producto tiene 'global_product_id', se considera transferido
            $isLocal = isset($product['global_product_id']);
            $formatted_product = [
                'id' => $isLocal ? $product['global_product_id'] : $product['id'],
                'price' => $isLocal ? $product['global_product']['public_price'] : $product['public_price'],
                'isLocal' => !$isLocal,
                'current_stock' => $product['current_stock'],
                'name' => $isLocal ? $product['global_product']['name'] : $product['name'],
                'image_url' => $isLocal ? $product['global_product']['media'][0]['original_url'] : $product['media'][0]['original_url'],
                'disabled' => false, //propiedad de deshabilitado para no mostrarlo en la creación de orden cuando ya se seleccionó
                'relative_id' => $relative_id // Asignamos el relative_id actual
            ];
            $relative_id++; // Incrementamos el contador
            return $formatted_product;
        }, $merged);

        return response()->json(compact('products'));
    }


    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 20;

        $online_orders = OnlineSale::where('store_id', auth()->user()->store_id)->latest()->get()->skip($offset)->take(20);

        return response()->json(['items' => $online_orders]);
    }


}
