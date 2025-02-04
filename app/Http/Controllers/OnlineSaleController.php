<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use App\Models\Client;
use App\Models\GlobalProductStore;
use App\Models\Logo;
use App\Models\OnlineSale;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Service;
use App\Models\Store;
use App\Notifications\OnlineSaleNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OnlineSaleController extends Controller
{
    public function index()
    {
        $banners = Banner::with(['media'])->where('store_id', auth()->user()->store_id)->first();
        $logo = Logo::with(['media'])->where('store_id', auth()->user()->store_id)->first();
        $cash_registers = CashRegister::where('store_id', auth()->user()->store_id)->get();
        $online_orders = OnlineSale::where('store_id', auth()->user()->store_id)->latest()->get()->take(20);
        $total_online_orders = OnlineSale::where('store_id', auth()->user()->store_id)->get()->count();
        $clients = Client::where('store_id', auth()->user()->store_id)->get(['id', 'name']);

        return inertia('OnlineSale/Index', compact('banners', 'logo', 'online_orders', 'cash_registers', 'total_online_orders', 'clients'));
    }

    public function clientIndex($encoded_store_id)
    {
        // Decodificar el ID de la tienda
        $store_id = base64_decode($encoded_store_id);

        $store_id = intval($store_id);

        // Buscar la tienda
        $store = Store::find($store_id);

        if (!$store) {
            return inertia('Error/404'); // Manejar caso de tienda no encontrada
        }

        // Obtener todos los productos (locales y transferidos)
        $all_products = $this->getAllProducts($store_id);
        // Contar el total de productos
        $total_products = $all_products->count();

        // Tomar solo los primeros 12 productos
        $products = $all_products->take(12);

        //servicios
        $services = Service::with('media')->where('store_id', $store_id)->get();
        $total_services = Service::where('store_id', $store_id)->get()->count();

        // Obtener los banners
        $banners = Banner::with(['media'])->where('store_id', $store_id)->first();

        // return $all_products;
        // Retornar la vista con los datos
        return inertia('OnlineSale/ClientIndex', compact('store', 'products', 'total_products', 'services', 'total_services', 'store_id', 'banners'));
    }

    public function cartIndex()
    {
        return inertia('OnlineSale/Cart');
    }

    public function create()
    {
        return inertia('OnlineSale/Create');
    }

    public function quoteService($service)
    {
        return inertia('OnlineSale/QuoteService');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:10',
            'payment_method' => 'nullable|string|max:255',
            'suburb' => 'required|string|max:255',
            'email' => 'nullable|email',
            'street' => 'required|string|max:255',
            'ext_number' => 'required|string|min:1|max:50',
            'int_number' => 'nullable|string|min:1|max:50',
            'postal_code' => 'nullable|string|max:6',
            'polity_state' => 'required|string|max:100',
            'address_references' => 'nullable|string|min:1|max:255',
            'products' => 'required|array|min:1',
            'delivery_price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'store_id' => 'required|numeric|min:0',
            'created_from_app' => 'boolean',
            'store_inventory' => 'boolean',
        ]);

        //eliminar productos vacios de la lista
        $validated['products'] = array_filter($request->products, function ($product) {
            return $product['product_id'];
        });

        $is_inventory_on = auth()->user()->store->settings()->where('key', 'Control de inventario')->first()?->pivot->value;
        if ($is_inventory_on) {
            $this->checkProductStock($validated['products']);
        }
        $this->updateProductStock($validated['products']);

        $new_online_sale = OnlineSale::create($validated);

        //codifica el id de la venta
        $encoded_store_id = base64_encode($request->store_id);

        if ($request->created_from_app === true) {
            return to_route('online-sales.show', $new_online_sale->id);
        } else {
            // notificar
            $store = Store::find($validated['store_id']);
            $store->users->each(function ($user) use ($new_online_sale) {
                $user->notify(new OnlineSaleNotification(
                    'Nuevo pedido en linea',
                    "Nuevo pedido a domicilio con folio $new_online_sale->id, de $new_online_sale->name",
                    route('online-sales.show', $new_online_sale->id),
                    'new',
                ));
            });
            return redirect()->route('online-sales.client-index', ['encoded_store_id' => $encoded_store_id]);
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

    public function showService($service)
    {
        $service = Service::with('media')->find($service);

        return inertia('OnlineSale/ShowService', compact('service'));
    }

    public function edit(OnlineSale $online_sale)
    {
        //
    }

    public function update(Request $request, OnlineSale $online_sale)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:10',
            'email' => 'nullable|email',
            'suburb' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'ext_number' => 'required|string|min:1|max:50',
            'int_number' => 'nullable|string|min:1|max:50',
            'postal_code' => 'nullable|string|max:6',
            'polity_state' => 'required|string|max:100',
            'address_references' => 'nullable|string|min:1|max:255',
            'products' => 'required|array|min:1',
            'delivery_price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'store_id' => 'required|numeric|min:0',
            'created_from_app' => 'boolean',
            'store_inventory' => 'boolean',
        ]);

        // Obtener los productos originales antes de la actualización
        $original_products = $online_sale->products;

        // Crear un array de productos originales para compararlos
        $original_products_array = [];
        foreach ($original_products as $product) {
            $original_products_array[$product['product_id']] = $product;
        }

        $new_products = array_filter($request->products, function ($product) {
            foreach ($product as $key => $value) {
                if (is_null($value)) {
                    return false;
                }
            }
            return true;
        });

        if ($request->store_inventory === true) {
            // Comparar productos y ajustar el stock en consecuencia
            foreach ($new_products as $new_product) {
                $product_id = $new_product['product_id'];
                $new_quantity = $new_product['quantity'];

                if (isset($original_products_array[$product_id])) {
                    $original_quantity = $original_products_array[$product_id]['quantity'];

                    if ($new_quantity > $original_quantity) {
                        // Aumentar cantidad
                        $difference = $new_quantity - $original_quantity;
                        $temp_product = $new_product['isLocal'] ? Product::find($product_id) : GlobalProductStore::find($product_id);
                        if ($temp_product->current_stock < $difference) {
                            throw ValidationException::withMessages([
                                'products' => 'No hay suficiente stock disponible de ' . $new_product['name'],
                            ]);
                        }
                        $temp_product->current_stock -= $difference;
                    } elseif ($new_quantity < $original_quantity) {
                        // Disminuir cantidad
                        $difference = $original_quantity - $new_quantity;
                        $temp_product = $new_product['isLocal'] ? Product::find($product_id) : GlobalProductStore::find($product_id);
                        $temp_product->current_stock += $difference;
                    }

                    unset($original_products_array[$product_id]);
                } else {
                    // Nuevo producto
                    $temp_product = $new_product['isLocal'] ? Product::find($product_id) : GlobalProductStore::find($product_id);
                    if ($temp_product->current_stock < $new_quantity) {
                        throw ValidationException::withMessages([
                            'products' => 'No hay suficiente stock disponible de ' . $new_product['name'],
                        ]);
                    }
                    $temp_product->current_stock -= $new_quantity;
                }

                $temp_product->save();
            }

            // Los productos restantes en $original_products_array fueron eliminados
            foreach ($original_products_array as $original_product) {
                $temp_product = $original_product['isLocal']
                    ? Product::find($original_product['product_id'])
                    : GlobalProductStore::find($original_product['product_id']);
                $temp_product->current_stock += $original_product['quantity'];
                $temp_product->save();
            }
        }

        $online_sale->update($validated);

        return to_route('online-sales.show', $online_sale->id);
    }

    public function destroy(OnlineSale $online_sale)
    {
        $online_sale->delete();
    }

    public function getAllProducts($store_id)
    {
        // productos creados localmente en la tienda que no están en el catálogo base o global y con permiso de mostrar en la tienda
        $local_products = Product::with(['category:id,name', 'brand:id,name', 'media'])
            ->where([
                'store_id' => $store_id,
                'show_in_online_store' => 1,
            ])
            ->latest('id')
            ->get(['id', 'name', 'public_price', 'code', 'store_id', 'category_id', 'brand_id', 'min_stock', 'max_stock', 'current_stock', 'product_on_request', 'bulk_product', 'measure_unit', 'days_for_delivery', 'currency']);

        // productos transferidos desde el catálogo base y con permiso de mostrar en la tienda
        $transfered_products = GlobalProductStore::with(['globalProduct' => ['media', 'category']])->where([
            'store_id' => $store_id,
            'show_in_online_store' => 1,
        ])->get();

        // Creamos un nuevo arreglo combinando los dos conjuntos de datos
        $merged = array_merge($local_products->toArray(), $transfered_products->toArray());
        $products = collect($merged);

        return $products;
    }

    // para index de tienda en linea para clientes. (carga por scroll)
    public function loadMoreProducts()
    {
        // Cargar más productos desde la base de datos tomando sólo los requeridos y saltando los ya cargados
        $all_products = $this->getAllProducts(request('storeId'));
        $moreProducts = $all_products->splice(request('offset'))->take(request('limit'));

        return response()->json(['products' => $moreProducts]);
    }

    public function fetchProduct($product_id, $is_local)
    {
        if ($is_local === 'true') {
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
            'cancel' => 'Cancelado',
            'refunded' => 'Reembolsado',
        ];

        // Verifica si el estado solicitado es válido
        if (!isset($status_map[$request->status])) {
            return response()->json(['error' => 'Estado inválido'], 400);
        }

        $status = $status_map[$request->status];
        $delivered_at = null;

        // Si se cambia desde 'delivered' a otro estado y existe una caja configurada
        // if ($online_sale->delivered_at && $request->online_sales_cash_register) {
        //     $total_sale = $online_sale->total + $online_sale->delivery_price;
        //     $cash_register = CashRegister::find($request->online_sales_cash_register);
        //     $cash_register->current_cash -= $total_sale;
        //     $cash_register->save();
        // }

        // Si se cambia el estado a 'delivered'
        if ($request->status == 'delivered') {
            $total_sale = $online_sale->total + $online_sale->delivery_price;
            // si existe una caja configurada
            if ($request->online_sales_cash_register) {
                $cash_register = CashRegister::find($request->online_sales_cash_register);
                $cash_register->current_cash += $total_sale;
                $cash_register->save();
            }
            $delivered_at = now();
        }

        // Si se cambia el estado a 'cancel' y la configuración de inventario está activa
        if ($request->status == 'cancel') {
            // foreach ($online_sale->products as $product) {
            //     if ($product['isLocal'] === true) {
            //         $temp_product = Product::find($product['id']);
            //         $temp_product->current_stock += $product['quantity']; // Aumentar el stock
            //         $temp_product->save();
            //     } else {
            //         $temp_product = GlobalProductStore::find($product['id']);
            //         $temp_product->current_stock += $product['quantity']; // Aumentar el stock
            //         $temp_product->save();
            //     }
            // }
            $this->cancel($online_sale);
        }

        if ($request->status == 'refunded') {
            $this->refund($online_sale);
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
            ->get(['id', 'name', 'public_price', 'current_stock', 'product_on_request', 'days_for_delivery']);

        // Productos transferidos desde el catálogo base
        $transfered_products = GlobalProductStore::with('globalProduct.media', 'globalProduct:id,name,public_price')
            ->where('store_id', auth()->user()->store_id)
            ->get();

        // Creamos un nuevo arreglo combinando los dos conjuntos de datos
        $merged = array_merge($local_products->toArray(), $transfered_products->toArray());

        // Inicializamos el contador para relative_id
        $relative_id = 1;

        // Construimos un nuevo arreglo con el formato especificado
        $products = array_map(function ($product) use (&$relative_id) {
            // Si el producto tiene 'global_product_id', se considera transferido
            $isLocal = isset($product['global_product_id']);
            $formatted_product = [
                'id' => $product['id'],
                'price' => $isLocal ? $product['global_product']['public_price'] : $product['public_price'],
                'isLocal' => !$isLocal,
                'current_stock' => $product['current_stock'],
                'name' => $isLocal ? $product['global_product']['name'] : $product['name'],
                'image_url' => $isLocal
                    ? ($product['global_product']['media'][0]['original_url'] ?? null)
                    : ($product['media'][0]['original_url'] ?? null),
                'disabled' => false, //propiedad de deshabilitado para no mostrarlo en la creación de orden cuando ya se seleccionó
                'relative_id' => $relative_id, // Asignamos el relative_id actual
                'product_on_request' => $product['product_on_request'] ?? null, // si es producto bajo pedido
                'days_for_delivery' => $product['days_for_delivery'] ?? null // si es producto bajo pedido
            ];
            $relative_id++; // Incrementamos el contador
            return $formatted_product;
        }, $merged);

        return response()->json(compact('products'));
    }

    public function getSalesByDate($date)
    {
        // Obtener las ventas registradas en la fecha recibida
        $sales = OnlineSale::with(['store:id,name'])
            ->where('store_id', auth()->user()->store_id)
            ->whereDate('delivered_at', $date)
            ->orWhereDate('refunded_at', $date)
            ->get();


        return response()->json(['items' => $sales]);
    }

    public function refund(OnlineSale $onlineSale)
    {
        // obtiene la caja registradora asignada al cajero
        $cash_register = CashRegister::find(auth()->user()->cash_register_id);
        $is_inventory_on = auth()->user()->store->settings()->where('key', 'Control de inventario')->first()?->pivot->value;
        $saleProducts = collect($onlineSale->products);
        $total_amount = $saleProducts->sum(fn($sale) => $sale['price'] * $sale['quantity']);
        $folio = 'L-' . $onlineSale->id;

        // Crear movimiento de retiro de caja con el monto de la venta a cancelar
        CashRegisterMovement::create([
            'amount' => $total_amount,
            'type' => 'Retiro',
            'notes' => "Venta con folio $folio fue reembolsada",
            'cash_register_id' => $cash_register->id,
        ]);
        // Restar dinero de caja
        if ($cash_register->current_cash < $total_amount) {
            $cash_register->update(['current_cash' => 0]);
        } else {
            $cash_register->decrement('current_cash', $total_amount);
        }

        // si el control de inventario esta activado, devolver mercancia disponible para la venta
        $updated_items = [];
        // if ($is_inventory_on) {
        $saleProducts->each(function ($sale) use ($folio, &$updated_items) {
            if ($sale['isLocal']) {
                $current_product = Product::find($sale['product_id']);
                $indexedDB_name = $current_product->name;
            } else {
                $current_product = GlobalProductStore::find($sale['product_id']);
                $indexedDB_name = $current_product->globalProduct->name;
            }

            $current_product->increment('current_stock', $sale['quantity']);

            //Registra el historial de venta de cada producto
            ProductHistory::create([
                'description' => "Registro de entrada de producto por reembolso de venta con folio $folio. " . $sale['quantity'] . ' pieza(s)',
                'type' => 'Reembolso',
                'historicable_id' => $current_product->id,
                'historicable_type' => get_class($current_product),
            ]);

            // guardar id formateado y stock actual en array para enviarlo al cliente y actualizar indexedDB
            $updated_items[] = ['name' => $indexedDB_name, 'current_stock' => $current_product->current_stock];
        });
        // }

        // marcar venta como reembolsada
        $onlineSale->update(['refunded_at' => now(), 'status' => 'Reembolsado']);

        return response()->json(compact('updated_items'));
    }

    public function cancel(OnlineSale $onlineSale)
    {
        $is_inventory_on = auth()->user()->store->settings()->where('key', 'Control de inventario')->first()?->pivot->value;
        $saleProducts = collect($onlineSale->products);
        $folio = 'L-' . $onlineSale->id;

        // si el control de inventario esta activado, devolver mercancia disponible para la venta
        $updated_items = [];
        // if ($is_inventory_on) {
        $saleProducts->each(function ($sale) use ($folio, &$updated_items) {
            if ($sale['isLocal']) {
                $current_product = Product::find($sale['product_id']);
                $indexedDB_name = $current_product->name;
            } else {
                $current_product = GlobalProductStore::find($sale['product_id']);
                $indexedDB_name = $current_product->globalProduct->name;
            }

            $current_product->increment('current_stock', $sale['quantity']);

            //Registra el historial de venta de cada producto
            ProductHistory::create([
                'description' => "Registro de entrada de producto por cancelación de venta con folio $folio. " . $sale['quantity'] . ' pieza(s)',
                'type' => 'Cancelación',
                'historicable_id' => $current_product->id,
                'historicable_type' => get_class($current_product),
            ]);

            // guardar id formateado y stock actual en array para enviarlo al cliente y actualizar indexedDB
            $updated_items[] = ['name' => $indexedDB_name, 'current_stock' => $current_product->current_stock];
        });
        // }

        // marcar venta como cancelada
        $onlineSale->update(['refunded_at' => now(), 'status' => 'Cancelado']);
        return response()->json(compact('updated_items'));
    }

    //para index en app
    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 20;

        $online_orders = OnlineSale::where('store_id', auth()->user()->store_id)->latest()->skip($offset)->take(20)->get();

        return response()->json(['items' => $online_orders]);
    }

    // PRIVATE
    private function checkProductStock(array $products)
    {
        foreach ($products as $product) {
            $temp_product = $product['isLocal'] ? Product::find($product['product_id']) : GlobalProductStore::find($product['product_id']);

            //revisa que no sea producto bajo pedido para no tomar en cuenta el stock
            if ($temp_product->current_stock < $product['quantity'] && !$product['product_on_request']) {
                throw ValidationException::withMessages([
                    'products' => 'No hay suficiente stock disponible de ' . $product['name'],
                ]);
            }
        }
    }

    private function updateProductStock(array $products)
    {
        foreach ($products as $product) {
            // Verifica si 'product_on_request' está definido y su valor es falso
            if (!($product['product_on_request'] ?? false)) {
                $temp_product = $product['isLocal']
                    ? Product::find($product['product_id'])
                    : GlobalProductStore::find($product['product_id']);

                if ($temp_product) {
                    if ($temp_product->current_stock < $product['quantity']) {
                        $temp_product->current_stock = 0;
                    } else {
                        $temp_product->current_stock -= $product['quantity'];
                    }
                    $temp_product->save();
                }
            }
        }
    }
}
