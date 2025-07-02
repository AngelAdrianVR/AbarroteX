<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use App\Models\Client;
use App\Models\CreditSaleData;
use App\Models\GlobalProductStore;
use App\Models\Installment;
use App\Models\OnlineSale;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Sale;
use App\Models\ServiceReport;
use App\Notifications\BasicNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Jetstream\Agent;

class SaleController extends Controller
{
    public function pointIndex()
    {
        $store_id = auth()->user()->store_id;
        // productos creados localmente en la tienda que no están en el catálogo base o global
        $local_products = Product::where('store_id', $store_id)
            ->latest()
            ->get(['id', 'name', 'code']);

        $products_quantity = Product::where('store_id', $store_id)->get()->count();

        // productos transferidos desde el catálogo base
        $transfered_products = GlobalProductStore::with(['globalProduct:id,name,code'])
            ->where('store_id', $store_id)
            ->get(['id', 'global_product_id']);

        // Convertimos $local_products a un arreglo asociativo
        $local_products_array = $local_products->toArray();

        // Creamos un nuevo arreglo combinando los dos conjuntos de datos
        $products = new Collection(array_merge($local_products_array, $transfered_products->toArray()));

        //recupera todas las cajas registradoras de la tienda
        $cash_registers = CashRegister::where('store_id', $store_id)->get();

        $clients = Client::where('store_id', $store_id)->get(['id', 'name']);

        // Redireccionar a la vista de acuerdo al dispositivo
        $agent = new Agent();
        if ($agent->isDesktop() || $agent->isLaptop()) {
            return inertia('Sale/Point', compact('products', 'cash_registers', 'clients', 'products_quantity'));
        } else {
            return inertia('Sale/PointMobile', compact('products', 'cash_registers', 'clients', 'products_quantity'));
        }
    }

    public function index()
    {
        // obtiene las cajas registradoras de la tienda
        $cash_registers = CashRegister::where('store_id', auth()->user()->store_id)->get();
        $groupedSales = null;
        $total_sales = 1;

        return inertia('Sale/Index', compact('groupedSales', 'total_sales', 'cash_registers'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $folio_stored = $this->storeEachProductSold($request->all());

        return response()->json(compact('folio_stored'));
    }

    public function show($date, $cashRegisterId)
    {
        $storeId = auth()->user()->store_id;
        $storeUsers = auth()->user()->store->users->pluck('id');

        // Obtener las ventas registradas en la fecha recibida
        $sales = Sale::with(['cashRegister:id,name', 'user:id,name'])
            ->where('store_id', $storeId)
            ->where('cash_register_id', $cashRegisterId) //recuperar solo las  ventas de la caja involucrada.
            ->whereDate('created_at', $date)
            ->get();

        $online_sales = OnlineSale::where('store_id', $storeId)
            ->whereDate('delivered_at', $date)
            ->orWhereDate('refunded_at', $date)
            ->get();

        // Obtener los abonos registrados en la fecha especificada y que pertenezcan a los usuarios de la tienda autenticada
        $installments = Installment::whereIn('user_id', $storeUsers)
            ->whereDate('created_at', $date)
            ->get();
        
        // Obtener los servicios entregados y pagados en la fecha especificada
        $order_services = ServiceReport::where(function ($query) {
            $query->where('status', 'Entregado/Pagado')
                ->orWhere('status', 'Cancelada');
        })
        ->where('store_id', auth()->user()->store_id)
        ->latest()
        ->get();

        $this->addCreditDataToSales($sales);

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $day_sales = $this->getGroupedSalesByDate($sales, $online_sales, $installments, true, $order_services);

        $date = Carbon::parse($date)->startOfDay(); // Comienza el día actual para la comparación

        // Obtener todas las ventas anteriores de ambas tablas
        $previous_sales = collect([
            ...Sale::where('store_id', $storeId)
                ->where('cash_register_id', $cashRegisterId)
                ->where('created_at', '<', $date)
                ->orderBy('created_at', 'desc')
                ->get(),
            ...OnlineSale::where('store_id', $storeId)
                ->where('created_at', '<', $date)
                ->orderBy('created_at', 'desc')
                ->get()
        ])->sortByDesc('created_at');

        // Obtener la fecha de la venta anterior más cercana
        $previous_sale = $previous_sales->first();
        $previous_sale_date = $previous_sale ? $previous_sale->created_at->toDateString() : null;

        // Obtener todas las ventas siguientes de ambas tablas
        $next_sales = collect([
            ...Sale::where('store_id', $storeId)
                ->where('cash_register_id', $cashRegisterId)
                ->where('created_at', '>', $date->endOfDay())
                ->orderBy('created_at', 'asc')
                ->get(),
            ...OnlineSale::where('store_id', $storeId)
                ->where('created_at', '>', $date->endOfDay())
                ->orderBy('created_at', 'asc')
                ->get()
        ])->sortBy('created_at');

        // Obtener la fecha de la venta siguiente más cercana
        $next_sale = $next_sales->first();
        $next_sale_date = $next_sale ? $next_sale->created_at->toDateString() : null;
        
        // return $day_sales;
        return inertia('Sale/Show', compact('day_sales', 'previous_sale_date', 'next_sale_date'));
    }

    public function edit(Sale $sale)
    {
        //
    }

    public function update(Request $request, Sale $sale)
    {
        //
    }

    public function destroy(Sale $sale)
    {
        // Obtener la fecha de creación del registro de venta
        $saleDate = $sale->created_at->toDateString();

        // Eliminar todos los registros que tengan la misma fecha de creación
        Sale::where('store_id', auth()->user()->store_id)->whereDate('created_at', $saleDate)->delete();

        // Eliminar el registro de venta enviado como referencia
        $sale->delete();
    }

    public function searchProduct(Request $request)
    {
        $queryDate = $request->input('queryDate');
        $startDate = Carbon::parse($queryDate[0])->startOfDay();
        $endDate = Carbon::parse($queryDate[1])->endOfDay();

        // Obtener las ventas registradas en el rango de fechas requerido por el filtro
        $sales = Sale::where('store_id', auth()->user()->store_id)
            ->where('cash_register_id', $request->input('cashRegisterId'))
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->get();

        $online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->get();

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $groupedSales = $this->getGroupedSalesByDate($sales, $online_sales);

        return response()->json(['items' => $groupedSales]);
    }

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 30;
        $sales = Sale::where('store_id', auth()->user()->store_id)
            ->where('cash_register_id', request('cashRgisterId'))
            ->latest()
            ->get();

        $online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
            ->latest()
            ->get();

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $groupedSales = $this->getGroupedSalesByDate($sales, $online_sales)->skip($offset)
            ->take(30);

        return response()->json(['items' => $groupedSales]);
    }

    public function printTicket($folio)
    {
        // Obtener las ventas registradas en la fecha recibida
        $sales = Sale::where('store_id', auth()->user()->store_id)->where('folio', $folio)->get();

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        // $day_sales = $this->getGroupedSalesByDate($sales, true);

        // return $sales;
        return inertia('Sale/PrintTicket', compact('sales'));
    }

    public function syncLocalstorage(Request $request)
    {
        //recorre el arreglo de ventas registradas.
        foreach ($request->sales as $sale) {
            //recorre el arreglo de productos registrados en la venta.
            $this->storeEachProductSold($sale, $sale['created_at']);
        }
    }

    private function storeEachProductSold($sale_data, $created_at = null)
    {
        $store_id = auth()->user()->store_id;
        // Generar un id unico para productos vendidos a este cliente
        $last_sale = Sale::where('store_id', $store_id)->latest('id')->first();
        $folio = $last_sale ? intval($last_sale->folio) + 1 : 1;

        // obtiene la caja registradora asignada al cajero
        $cash_register = CashRegister::find(auth()->user()->cash_register_id);

        // total de dinero en venta
        $total_amount = 0;
        foreach ($sale_data['saleProducts'] as $sale) {
            $total_amount += $sale['product']['public_price'] * $sale['quantity'];
            $is_global_product = explode('_', $sale['product']['id'])[0] == 'global';
            $product_id = explode('_', $sale['product']['id'])[1];

            $product_name = $sale['product']['name'];
            if (auth()->user()->store->type == 'Boutique / Tienda de Ropa / Zapatería') {
                // agregar color y talla al nombre
                $product_name .= "({$sale['product']['additional']['color']['name']}-{$sale['product']['additional']['size']['name']})";
            }

            // obtiene un estracto para referir a la promoción en caso de tener
            $promotions = $this->getPromotion($sale);

            //regiatra cada producto vendido
            Sale::create([
                'current_price' => $sale['product']['public_price'],
                'discounted_price' => array_key_exists('discounted_price', $sale['product']) ? $sale['product']['discounted_price'] : null,
                'promotions_applied' => $promotions,
                'quantity' => $sale['quantity'],
                'folio' => $folio,
                'payment_method' => $sale_data['paymentMethod'],
                'product_name' => $product_name,
                'product_id' => $product_id,
                'is_global_product' => $is_global_product,
                'original_price' => $sale['originalPrice'],
                'client_id' => $sale_data['client_id'] == false ? null : $sale_data['client_id'],
                'store_id' => $store_id,
                'cash_register_id' => auth()->user()->cash_register_id,
                'user_id' => auth()->id(),
                'created_at' => $created_at ?? now(),
            ]);

            //Desontar cantidades del stock de cada producto vendido (sólo si se configura para tomar en cuenta el inventario).
            $is_inventory_on = auth()->user()->store->settings()->where('key', 'Control de inventario')->first()?->pivot->value;
            // if ($is_inventory_on) {
            $current_product = $is_global_product
                ? GlobalProductStore::find($product_id)
                : Product::find($product_id);

            if ($current_product->current_stock <= $sale['quantity']) {
                $current_product->update(['current_stock' => 0]);

                if ($is_inventory_on) {
                    // notificar si no hay existencias
                    $title = "Sin stock!";
                    $description = "Te has quedado sin existencias del producto <span class='text-primary'>$product_name</span>";
                    $url =  $is_global_product
                        ? route('global-product-store.show', base64_encode($current_product->id))
                        : route('products.show', base64_encode($current_product->id));

                    auth()->user()->notify(new BasicNotification($title, $description, $url));
                }
            } else {
                $current_product->decrement('current_stock', $sale['quantity']);

                // notificar si ha llegado al limite de existencias bajas
                if ($current_product->current_stock <= $current_product->min_stock && $is_inventory_on) {
                    $title = "Bajo stock!";
                    $description = "Producto <span class='text-primary'>$product_name</span> alcanzó el nivel mínimo establecido";
                    $url =  $is_global_product
                        ? route('global-product-store.show', base64_encode($current_product->id))
                        : route('products.show', base64_encode($current_product->id));
                    auth()->user()->notify(new BasicNotification($title, $description, $url));
                }
            }
            // }
            //Registra el historial de venta de cada producto
            $size = '';
            if ($current_product->additional) {
                // agregar color y talla al historial si es que la tiene
                $size = ' talla ' . $current_product->additional['size']['name'] . ' color ' . $current_product->additional['color']['name'];
            }
            ProductHistory::create([
                'description' => 'Registro de venta. ' . $sale['quantity'] . ' pieza(s)' . $size,
                'type' => 'Venta',
                'user_id' => auth()->id(),
                'historicable_id' => $product_id,
                'historicable_type' => $is_global_product
                    ? GlobalProductStore::class
                    : Product::class,
                'created_at' => $created_at ?? now(),
            ]);
        }

        //Crea registro de venta a crédito si así lo fue
        if ($sale_data['has_credit']) {
            $new_credit_sale_data = CreditSaleData::create([
                'folio' => $folio,
                'store_id' => $store_id,
                'client_id' => $sale_data['client_id'],
                'expired_date' => $sale_data['limit_date'],
                'status' => $sale_data['deposit'] ? 'Parcial' : 'Pendiente',
            ]);

            //si hay primer abono el dia de la compra se registra
            if ($sale_data['deposit']) {
                Installment::create([
                    'amount' => $sale_data['deposit'],
                    'notes' => 'Primer abono hecho en la compra',
                    'credit_sale_data_id' => $new_credit_sale_data->id,
                    'user_id' => auth()->id(),
                ]);

                //crea un movimiento de caja para guardar el abono si paga en efectivo y no con tarjeta
                CashRegisterMovement::create([
                    'amount' => $sale_data['deposit'],
                    'type' => 'Ingreso',
                    'notes' => 'Primer abono hecho en la venta con folio ' . $folio,
                    'cash_register_id' => auth()->user()->cash_register_id,
                ]);

                //Suma la cantidad abonada
                $cash_register->current_cash += $sale_data['deposit'];
                $cash_register->save();
            }

            // sumar la deuda al cliente tomando en cuenta el abono
            $client = Client::find($sale_data['client_id']);
            if ($client->debt) {
                $client->debt += $total_amount - $sale_data['deposit'];
            } else {
                $client->debt = $total_amount - $sale_data['deposit'];
            }

            $client->save();
        } else if ($sale_data['paymentMethod'] === 'Efectivo') {
            //Suma la cantidad total de dinero vendido del producto al dinero actual de la caja si el pago fue en efectivo y no con tarjeta
            $cash_register->current_cash += $total_amount;
            $cash_register->save();
        }

        return $folio;
    }

    public function fetchCashRegisterSales($cash_register_id)
    {
        // Obtener todas las ventas registradas y contar el número de agrupaciones por día
        $total_sales = DB::table('sales')
            ->select(DB::raw('DATE(created_at) as date'))
            ->where('store_id', auth()->user()->store_id)
            ->where('cash_register_id', $cash_register_id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->count();
        // Filtrar las ventas por store_id y cash_register_id
        $sales = Sale::where('store_id', auth()->user()->store_id)
            ->where('cash_register_id', $cash_register_id)
            ->latest()
            ->get();

        $online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
            ->latest()
            ->get();

        $order_services = ServiceReport::where(function ($query) {
            $query->where('status', 'Entregado/Pagado')
                ->orWhere('status', 'Cancelada');
        })
        ->where('store_id', auth()->user()->store_id)
        ->latest()
        ->get(['id', 'folio', 'total_cost', 'paid_at', 'created_at']);

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $groupedSales = $this->getGroupedSalesByDate($sales, $online_sales, null, false, $order_services)->take(30);
        // $groupedSales = $this->getGroupedSalesByDate($sales, $online_sales)->take(30);

        // Retornar los datos agrupados
        return response()->json(['groupedSales' => $groupedSales, 'total_sales' => $total_sales]);
    }

    public function refund($saleFolio)
    {
        // obtiene la caja registradora asignada al cajero
        $cash_register = CashRegister::find(auth()->user()->cash_register_id);
        $is_inventory_on = auth()->user()->store->settings()->where('key', 'Control de inventario')->first()?->pivot->value;
        $saleProducts = Sale::where([
            'store_id' => auth()->user()->store_id,
            'folio' => $saleFolio,
        ])->get();
        $total_amount = $saleProducts->sum(fn($sale) => $sale->current_price * $sale->quantity);

        // Crear movimiento de retiro de caja con el monto de la venta a cancelar
        CashRegisterMovement::create([
            'amount' => $total_amount,
            'type' => 'Retiro',
            'notes' => "Venta con folio $saleFolio fue reembolsada",
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
        $saleProducts->each(function ($sale) use ($saleFolio, &$updated_items) {
            if ($sale->is_global_product) {
                $current_product = GlobalProductStore::find($sale->product_id);
                $indexedDB_name = $current_product->globalProduct->name;
            } else {
                $current_product = Product::find($sale->product_id);
                $indexedDB_name = $current_product->name;
            }

            $current_product->increment('current_stock', $sale->quantity);

            //Registra el historial de venta de cada producto
            ProductHistory::create([
                'description' => "Registro de entrada de producto por reembolso de venta con folio $saleFolio. " . $sale['quantity'] . ' pieza(s)',
                'type' => 'Reembolso',
                'user_id' => auth()->id(),
                'historicable_id' => $current_product->id,
                'historicable_type' => get_class($current_product),
            ]);

            // guardar id formateado y stock actual en array para enviarlo al cliente y actualizar indexedDB
            $updated_items[] = ['name' => $indexedDB_name, 'current_stock' => $current_product->current_stock];
        });
        // }

        // marcar productos de venta como reembolsados / cancelados
        $saleProducts->each(fn($sale) => $sale->update(['refunded_at' => now()]));
        // marcar status de informacion de credoto a reembolsado (si es que es a credito)
        $credit_sale_data = CreditSaleData::firstWhere('folio', $saleFolio);
        if ($credit_sale_data) {
            $credit_sale_data->update(['status' => 'Reembolsado']);
            $credit_sale_data->client->debt = $credit_sale_data->client->calcTotalDebt();
            $credit_sale_data->client->save();
        }

        return response()->json(compact('updated_items'));
    }

    public function updateGroupSale(Request $request)
    {
        // Validar los datos del request
        $request->validate([
            'folio' => 'required|string',
            'products' => 'required|array',
            'products.*.id' => 'required|integer|exists:sales,id',
            'products.*.product_id' => 'required|string',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.current_price' => 'required|numeric|min:0',
        ], [
            'required' => 'llenar campo'
        ]);

        // Obtener las ventas actuales
        $sales = Sale::where([
            'store_id' => auth()->user()->store_id,
            'folio' => $request->folio,
        ])->get();

        $products_in_request = collect($request->products);

        // Obtener información de la caja registradora y control de inventario
        $cash_register = CashRegister::find(auth()->user()->cash_register_id);
        $is_inventory_on = auth()->user()->store->settings()->where('key', 'Control de inventario')->first()?->pivot->value;

        // Variables para calcular las diferencias de monto
        $total_diff_amount = 0;

        // Procesar cada venta y actualizar los productos e inventario
        $sales->each(function ($sale) use ($products_in_request, $is_inventory_on, &$total_diff_amount, $request) {
            $sale_updated = $products_in_request->firstWhere('id', $sale->id);

            if ($sale_updated) {
                $old_quantity = $sale->quantity;
                $old_price = $sale->current_price;
                $new_quantity = $sale_updated['quantity'];
                $new_price = $sale_updated['current_price'];

                $product_type = explode('_', $sale_updated['product_id'])[0];
                $product_id = explode('_', $sale_updated['product_id'])[1];
                $product_name = $product_type == 'global'
                    ? GlobalProductStore::find($product_id)->globalProduct->name
                    : Product::find($product_id)->name;

                // Calcular diferencia en inventario
                // if ($is_inventory_on) {
                $current_product = $sale->is_global_product
                    ? GlobalProductStore::find($sale->product_id)
                    : Product::find($sale->product_id);

                if ($old_quantity != $new_quantity || $sale->product_id != $product_id) {
                    if ($sale->product_id != $product_id) {
                        $current_product->increment('current_stock', $old_quantity);
                        $current_product_name = $sale->is_global_product
                            ? $current_product->globalProduct->name
                            : $current_product->name;

                        // registrar regreso de producto a stock de viejo producto
                        if (auth()->user()->store->type == 'Boutique / Tienda de Ropa / Zapatería' && $current_product->additional) {
                            $refund_description = "Registro de devolución por reemplazo de producto en la venta con folio $request->folio. " . $old_quantity
                                . " pieza(s) de talla {$current_product->additional['size']['name']} y color {$current_product->additional['color']['name']}";
                            $sale_description = "Registro de venta por reemplazo del producto $current_product_name por este en talla {$current_product->additional['size']['name']} y color {$current_product->additional['color']['name']} para la venta con folio $request->folio. "
                                . $new_quantity . ' pieza(s)';
                        } else {
                            $refund_description = "Registro de devolución por reemplazo de producto en la venta con folio $request->folio. " . $old_quantity . ' pieza(s)';
                            $sale_description = "Registro de venta por reemplazo del producto $current_product_name por este en la venta con folio $request->folio. " . $new_quantity . ' pieza(s)';
                        }
                        ProductHistory::create([
                            'description' => $refund_description,
                            'type' => 'Edición',
                            'user_id' => auth()->id(),
                            'historicable_id' => $current_product->id,
                            'historicable_type' => get_class($current_product),
                        ]);
                        $new_product = $sale->is_global_product
                            ? GlobalProductStore::find($product_id)
                            : Product::find($product_id);

                        $new_stock = $new_product->current_stock - $new_quantity;
                        $new_product->current_stock = max($new_stock, 0);
                        $new_product->save();
                        ProductHistory::create([
                            'description' => $sale_description,
                            'type' => 'Edición',
                            'user_id' => auth()->id(),
                            'historicable_id' => $new_product->id,
                            'historicable_type' => get_class($new_product),
                        ]);
                    } else {
                        $current_product->increment('current_stock', $old_quantity);

                        $new_stock = $current_product->current_stock - $new_quantity;
                        $current_product->current_stock = max($new_stock, 0);
                        $current_product->save();

                        if (auth()->user()->store->type == 'Boutique / Tienda de Ropa / Zapatería' && $current_product->additional) {
                            if ($old_quantity < $new_quantity) {
                                $abs_quantity = $new_quantity - $old_quantity;
                                $description = "Registro de más producto vendido por edición de la venta con folio $request->folio. "
                                    .  $abs_quantity . ' pieza(s) de talla ' . $current_product->additional['size']['name'] . ' y color ' . $current_product->additional['color']['name'];
                                $type = "Edición";
                            } else {
                                $abs_quantity = $old_quantity - $new_quantity;
                                $description = "Registro de devolución de producto por edición de la venta con folio $request->folio. "
                                    .  $abs_quantity . ' pieza(s) de talla ' . $current_product->additional['size']['name'] . ' y color ' . $current_product->additional['color']['name'];
                                $type = "Edición";
                            }
                        } else {
                            if ($old_quantity < $new_quantity) {
                                $abs_quantity = $new_quantity - $old_quantity;
                                $description = "Registro de más producto vendido por edición de la venta con folio $request->folio. " .  $abs_quantity . ' pieza(s)';
                                $type = "Edición";
                            } else {
                                $abs_quantity = $old_quantity - $new_quantity;
                                $description = "Registro de devolución de producto por edición de la venta con folio $request->folio. " .  $abs_quantity . ' pieza(s)';
                                $type = "Edición";
                            }
                        }

                        // movimiento de producto por edicion
                        ProductHistory::create([
                            'description' => $description,
                            'type' => $type,
                            'user_id' => auth()->id(),
                            'historicable_id' => $current_product->id,
                            'historicable_type' => get_class($current_product),
                        ]);
                    }
                }
                // }

                // Calcular la diferencia en montos para movimientos de caja
                $old_total = $old_quantity * $old_price;
                $new_total = $new_quantity * $new_price;
                $total_diff_amount += $new_total - $old_total;

                // agregar detalles de prenda a nombre
                if (auth()->user()->store->type == 'Boutique / Tienda de Ropa / Zapatería' && $current_product->additional) {
                    $product_name .= " ({$current_product->additional['color']['name']}-{$current_product->additional['size']['name']})";
                }

                // Actualizar la venta
                $sale->update([
                    'product_id' => $product_id,
                    'product_name' => $product_name,
                    'quantity' => $new_quantity,
                    'current_price' => $new_price,
                ]);
            }
        });

        // Crear movimiento de caja según la diferencia calculada
        if ($total_diff_amount != 0) {
            $movement_type = $total_diff_amount > 0 ? 'Ingreso' : 'Retiro';
            // CashRegisterMovement::create([
            //     'amount' => abs($total_diff_amount),
            //     'type' => $movement_type,
            //     'notes' => "Actualización de venta con folio {$request->folio}",
            //     'cash_register_id' => $cash_register->id,
            // ]);

            if ($movement_type == 'Ingreso') {
                $cash_register->increment('current_cash', abs($total_diff_amount));
            } else {
                $cash_register->decrement('current_cash', abs($total_diff_amount));
            }
        }
    }

    /**
     * Agrupa las ventas por fecha, incluyendo ventas normales, cotizaciones y ventas en línea.
     *
     * @param Collection|null $sales Ventas normales de tienda.
     * @param Collection|null $onlineSales Ventas en línea.
     * @param Collection|null $installments Ventas a plazos.
     * @param bool $returnSales Indica si se deben retornar los detalles de las ventas agrupadas.
     * @return Collection
     */
    private function getGroupedSalesByDate(
        $sales = null,
        $onlineSales = null,
        $installments = null,
        bool $returnSales = false,
        $serviceOrders = null, // 👈 ordenes de servicio para apontephone
    ): Collection {
        // Asegurarse de que las colecciones no sean nulas
        $sales = collect($sales);
        $onlineSales = collect($onlineSales);
        $installments = collect($installments);
        $serviceOrders = collect($serviceOrders);
        // 1. Filtrar ventas en línea (solo las entregadas o reembolsadas)
        $filteredOnlineSales = $onlineSales->filter(function ($onlineSale) {
            return $onlineSale->delivered_at || $onlineSale->refunded_at;
        });
        
        // 2. Combinar todas las ventas para agruparlas por fecha
        $allSales = $sales->merge($filteredOnlineSales)->merge($serviceOrders);

        return $allSales->groupBy(function ($sale) {
            if (isset($sale->current_price)) {
                return Carbon::parse($sale->created_at)->toDateString(); // Venta normal o cotización
            } elseif ($sale instanceof OnlineSale) { // Venta en linea
                if ($sale->delivered_at) {
                    return Carbon::parse($sale->delivered_at)->toDateString();
                } elseif ($sale->refunded_at) {
                    return Carbon::parse($sale->refunded_at)->toDateString();
                } else {
                    return Carbon::parse($sale->created_at)->toDateString(); // fallback
                }
            } else {
                return Carbon::parse($sale->paid_at)->toDateString(); // Orden de servicio
            }
         })->map(function ($dailySales) use ($returnSales, $installments) {
            // 3. Clasificar las ventas del día
            $normalSales = $dailySales->filter(fn($sale) => isset($sale->current_price) && !$sale->quote_id);
            $quoteSales = $dailySales->filter(fn($sale) => isset($sale->current_price) && $sale->quote_id);
            $onlineSales = $dailySales->filter(fn($sale) => $sale instanceof OnlineSale);
            $serviceOrdersGroup = $dailySales->filter(fn($sale) => $sale instanceof \App\Models\ServiceReport);


            // 4. Calcular totales y cantidades
            $totalSale = $normalSales->sum(fn($sale) => $this->calculateSaleAmount($sale));
            $totalQuotesSale = $quoteSales->sum(fn($sale) => $this->calculateQuoteSaleAmount($sale));
            $totalServiceOrders = $serviceOrdersGroup->sum('total_cost'); // total_cost + advance_payment
            $totalOnlineSale = $onlineSales->sum(function ($onlineSale) {
                return ($onlineSale->status == 'Entregado' || $onlineSale->status == 'Reembolsado')
                    ? $onlineSale->total + $onlineSale->delivery_price
                    : 0;
            });

            // 5. Calcular cantidades de productos
            $totalQuantityNormalSale = $normalSales->sum('quantity');
            $totalQuantityQuoteSale = $quoteSales->sum('quantity');
            $totalQuantityOnlineSale = $onlineSales->sum(fn($onlineSale) => count($onlineSale->products ?? []));
            $totalServiceFolios = $serviceOrdersGroup->unique('folio')->count();

            // 6. Calcular folios únicos
            $normalFolios = $normalSales->unique('folio')->count();
            $quoteFolios = $quoteSales->unique('folio')->count();
            $onlineFolios = $onlineSales->count();
            

            // 7. Agrupar ventas por folio si returnSales es true
            $normalSalesByFolio = $returnSales ? $normalSales->groupBy('folio')->map(fn($folioSales) => $this->formatNormalSaleByFolio($folioSales)) : [];
            $quoteSalesByFolio = $returnSales ? $quoteSales->groupBy('folio')->map(fn($folioSales) => $this->formatQuoteSaleByFolio($folioSales)) : [];

            return [
                'total_normal_quantity' => $totalQuantityNormalSale,
                'total_quote_quantity' => $totalQuantityQuoteSale,
                'total_online_quantity' => $totalQuantityOnlineSale,
                'total_sale' => $totalSale,
                'total_quotes_sale' => $totalQuotesSale,
                'online_sales_total' => $totalOnlineSale,
                'total_day_sale' => $totalSale + $totalQuotesSale + $totalOnlineSale,
                'normal_folios' => $normalFolios,
                'quote_folios' => $quoteFolios,
                'online_folios' => $onlineFolios,
                'sales' => $normalSalesByFolio,
                'quote_sales' => $quoteSalesByFolio,
                'online_sales' => $returnSales ? $onlineSales->values() : [],
                'installments' => $returnSales ? $installments->values() : [],
                'total_service_orders' => $totalServiceOrders,
                'service_folios' => $totalServiceFolios,
                'total_day_sale' => $totalSale + $totalQuotesSale + $totalOnlineSale + $totalServiceOrders,
                'service_orders' => $returnSales ? $serviceOrdersGroup->values() : [],
            ];
        });
    }

    /**
     * Calcula el monto de una venta normal o de cotización.
     *
     * @param object $sale
     * @return float
     */
    private function calculateSaleAmount(object $sale): float
    {
        $priceToUse = ($sale->discounted_price !== null && $sale->discounted_price >= 0)
            ? $sale->discounted_price
            : $sale->current_price;

        return $sale->quantity * $priceToUse;
    }

    /**
     * Calcula el monto de una venta de cotización, incluyendo descuentos e IVA.
     *
     * @param object $sale
     * @return float
     */
    private function calculateQuoteSaleAmount(object $sale): float
    {
        $baseAmount = $this->calculateSaleAmount($sale);
        $discounted = 0;

        if (isset($sale->quote->is_percentage_discount)) {
            $discounted = $sale->quote->is_percentage_discount
                ? $sale->quote->percentage * 0.01 * $sale->quote->total
                : $sale->quote->discount;
        }

        $iva = 0;
        if (isset($sale->quote->iva_included) && $sale->quote->iva_included === false) {
            $iva = $sale->quote->total * 0.16;
        }

        return $baseAmount + ($sale->quote->delivery_cost ?? 0) + $iva - $discounted;
    }

    /**
     * Formatea los datos de una venta normal agrupada por folio.
     *
     * @param Collection $folioSales
     * @return array
     */
    private function formatNormalSaleByFolio(Collection $folioSales): array
    {
        $firstSale = $folioSales->first();

        return [
            'products' => $folioSales->map(fn($sale) => $this->mapProductDetails($sale))->values(),
            'credit_data' => $firstSale->credit_data,
            'folio' => $firstSale->folio,
            'user_name' => $firstSale->user->name,
            'client_name' => $firstSale->client?->name ?? 'Público en general',
            'total_sale' => $folioSales->sum(fn($sale) => $this->calculateSaleAmount($sale)),
        ];
    }

    /**
     * Formatea los datos de una venta de cotización agrupada por folio.
     *
     * @param Collection $folioSales
     * @return array
     */
    private function formatQuoteSaleByFolio(Collection $folioSales): array
    {
        $firstSale = $folioSales->first();

        return [
            'products' => $folioSales->map(fn($sale) => $this->mapProductDetails($sale, true))->values(),
            'credit_data' => $firstSale->credit_data,
            'quote' => $firstSale->quote,
            'folio' => $firstSale->folio,
            'user_name' => $firstSale->user->name,
            'client_name' => $firstSale->client?->name ?? 'Público en general',
            'total_sale' => $folioSales->sum(fn($sale) => $this->calculateQuoteSaleAmount($sale)),
        ];
    }

    /**
     * Mapea los detalles de un producto de venta.
     *
     * @param object $sale
     * @param bool $isQuote Indica si es una venta de cotización para incluir 'quote_id'.
     * @return array
     */
    private function mapProductDetails(object $sale, bool $isQuote = false): array
    {
        $details = [
            'id' => $sale->id,
            'current_price' => $sale->current_price,
            'discounted_price' => $sale->discounted_price,
            'promotions_applied' => $sale->promotions_applied,
            'product_name' => $sale->product_name,
            'product_id' => $sale->product_id,
            'is_global_product' => $sale->is_global_product,
            'quantity' => $sale->quantity,
            'original_price' => $sale->original_price,
            'payment_method' => $sale->payment_method,
            'refunded_at' => $sale->refunded_at,
            'cash_register_id' => $sale->cash_register_id,
            'store_id' => $sale->store_id,
            'created_at' => $sale->created_at,
            'updated_at' => $sale->updated_at,
        ];

        if ($isQuote) {
            $details['quote_id'] = $sale->quote_id;
        }

        return $details;
    }

    private function addCreditDataToSales($sales)
    {
        $folios = $sales->unique('folio')->pluck('folio');

        $folios->each(function ($folio) use ($sales) {
            // Buscar CreditSaleData relacionado usando el folio
            $creditData = CreditSaleData::where([
                'folio' => $folio,
                'store_id' => auth()->user()->store_id,
            ])->first();

            if ($creditData) {
                // Obtener los installments relacionados
                $installments = $creditData->installments;

                // Agregar credit_data a las ventas del mismo folio
                $sales->where('folio', $folio)->each(function ($sale) use ($creditData, $installments) {
                    $sale->credit_data = [
                        'id' => $creditData->id,
                        'expired_date' => $creditData->expired_date,
                        'status' => $creditData->status,
                        'installments' => $installments,
                    ];
                });
            } else {
                // Si no hay credit_data, agregar un array vacío a las ventas del mismo folio
                $sales->where('folio', $folio)->each(function ($sale) {
                    $sale->credit_data = null;
                });
            }
        });
    }

    private function getPromotion($sale)
    {
        if (!$sale['product']['promotions']) {
            return null;
        }

        $promotions_applied = [];
        $product = $sale['product'];

        foreach ($sale['product']['promotions'] as $promo) {
            // si la promoción en curso no fue alicada, pasa al siguiente
            if (!$promo['applied']) continue;

            // calcular descuento
            if ($promo['type'] == 'Producto gratis al comprar otro') {
                $total_discounted = 0.0;
            } else {
                $original_price_total = $product['public_price'] * $sale['quantity'];
                $discount_price_total = $product['discounted_price'] * $sale['quantity'];
                $total_discounted = (float) number_format(($original_price_total - $discount_price_total), 1);
            }

            if ($promo['type'] == 'Descuento en precio fijo') {
                $promotions_applied[] = [
                    'discount' => $total_discounted,
                    'description' => "Descuento de $" . $product['public_price'] . " a $" . $product['discounted_price']
                ];
            } elseif ($promo['type'] == 'Descuento en porcentaje') {
                $promotions_applied[] = [
                    'discount' => $total_discounted,
                    'description' => "Descuento del {$promo['discount']}% (" . $product['public_price'] . " a $" . $product['discounted_price'] . ")"
                ];
            } elseif ($promo['type'] == 'Precio especial por paquete') {
                $promotions_applied[] = [
                    'discount' => $total_discounted,
                    'description' => "Precio especial por paquete: {$promo['pack_quantity']} a $" . $promo['pack_price']
                ];
            } elseif ($promo['type'] == 'Promoción tipo 2x1 o 3x2') {
                $promotions_applied[] = [
                    'discount' => $total_discounted,
                    'description' => "{$promo['buy_quantity']}x{$promo['pay_quantity']}"
                ];
            } elseif ($promo['type'] == 'Producto gratis al comprar otro') {
                if ($promo['giftable_type'] == Product::class) {
                    $giftable = Product::find($promo['giftable_id']);
                    $gift_name = $giftable->name;
                } else {
                    $giftable = GlobalProductStore::with(['globalProduct'])->find($promo['giftable_id']);
                    $gift_name = $giftable->globalProduct->name;
                }

                $promotions_applied[] = [
                    'discount' => $total_discounted,
                    'description' => "En la compra de {$promo['min_quantity_to_gift']}, gratis {$promo['quantity_to_gift']} $gift_name"
                ];
            } else {
                return "Promoción desconocida";
            }
        }

        return $promotions_applied;
    }
}
