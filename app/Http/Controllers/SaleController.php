<?php

namespace App\Http\Controllers;

use App\Models\CashCut;
use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use App\Models\Client;
use App\Models\CreditSaleData;
use App\Models\GlobalProductStore;
use App\Models\OnlineSale;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Sale;
use App\Notifications\BasicNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        return inertia('Sale/Point', compact('products', 'cash_registers', 'clients', 'products_quantity'));
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
        $this->storeEachProductSold($request->data['saleProducts']);

        //me mando al punto de venta en respuesta la nueva venta creada para imprimirla en caso de tener la oopción de impresión automática activada
        $new_sale = Sale::where('store_id', auth()->user()->store_id)->latest()->first();

        return response()->json(compact('new_sale'));
    }

    public function show($date, $cashRegisterId)
    {
        $storeId = auth()->user()->store_id;
        // Obtener las ventas registradas en la fecha recibida
        $sales = Sale::with(['cashRegister:id,name', 'user:id,name'])
            ->where('store_id', $storeId)
            ->where('cash_register_id', $cashRegisterId) //recuperar solo las  ventas de la caja involucrada.
            ->whereDate('created_at', $date)
            ->get();

        $online_sales = OnlineSale::where('store_id', $storeId)
            ->whereDate('created_at', $date)
            ->get();

        $this->addCreditDataToSales($sales);

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $day_sales = $this->getGroupedSalesByDate($sales, $online_sales, true);

        $date = Carbon::parse($date)->startOfDay(); // Comienza el día actual para la comparación

        // Obtener la fecha de la venta anterior
        $previous_sale = Sale::where('store_id', $storeId)
            ->where('cash_register_id', $cashRegisterId)
            ->where('created_at', '<', $date) // Excluir la fecha actual
            ->orderBy('created_at', 'desc')
            ->first();
        $previous_sale_date = $previous_sale ? $previous_sale->created_at->toDateString() : null;

        // Obtener la fecha de la venta siguiente
        $next_sale = Sale::where('store_id', $storeId)
            ->where('cash_register_id', $cashRegisterId)
            ->where('created_at', '>', $date->endOfDay()) // Excluir la fecha actual
            ->orderBy('created_at', 'asc')
            ->first();
        $next_sale_date = $next_sale ? $next_sale->created_at->toDateString() : null;

        //evalúa si la venta está dentro del corte---------------
        // $last_cash_cut = CashCut::where('store_id', auth()->user()->store_id)->where('cash_register_id', $cashRegisterId)->latest()->first();

        // si el corte tiene una fecha posterior a la venta entonces esta fuera de corte
        // y no se muestran las opciones de editar y reembolso.
        // if ($last_cash_cut && $last_cash_cut?->created_at > $sales[0]->created_at) {
        //     $is_out_of_cash_cut = true;
        // } else {
        //     $is_out_of_cash_cut = false;
        // }
        $is_out_of_cash_cut = false;

        // return $day_sales;

        return inertia('Sale/Show', compact('day_sales', 'is_out_of_cash_cut', 'previous_sale_date', 'next_sale_date'));
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
            $this->storeEachProductSold($sale['saleProducts'], $sale['created_at']);
        }
    }

    private function storeEachProductSold($sold_products, $created_at = null)
    {
        // Generar un id unico para productos vendidos a este cliente
        $last_sale = Sale::where('store_id', auth()->user()->store_id)->latest('id')->first();
        $folio = $last_sale ? intval($last_sale->folio) + 1 : 1;
        // obtiene la caja registradora asignada al cajero
        $cash_register = CashRegister::find(auth()->user()->cash_register_id);

        foreach ($sold_products as $product) {
            $is_global_product = explode('_', $product['product']['id'])[0] == 'global';
            $product_id = explode('_', $product['product']['id'])[1];

            $product_name = $product['product']['name'];

            //regiatra cada producto vendido
            Sale::create([
                'current_price' => $product['product']['public_price'],
                'quantity' => $product['quantity'],
                'folio' => $folio,
                'product_name' => $product_name,
                'product_id' => $product_id,
                'is_global_product' => $is_global_product,
                'price_changed' => $product['priceChanged'],
                'store_id' => auth()->user()->store_id,
                'cash_register_id' => auth()->user()->cash_register_id,
                'user_id' => auth()->id(),
                'created_at' => $created_at ?? now(),
            ]);

            //Suma la cantidad total de dinero vendido del producto al dinero actual de la caja
            $cash_register->current_cash += $product['product']['public_price'] * $product['quantity'];
            $cash_register->save();

            //Registra el historial de venta de cada producto
            ProductHistory::create([
                'description' => 'Registro de venta. ' . $product['quantity'] . ' piezas',
                'type' => 'Venta',
                'historicable_id' => $product_id,
                'historicable_type' => $is_global_product
                    ? GlobalProductStore::class
                    : Product::class,
                'created_at' => $created_at ?? now(),
            ]);

            //Desontar cantidades del stock de cada producto vendido (sólo si se configura para tomar en cuenta el inventario).
            $is_inventory_on = auth()->user()->store->settings()->where('key', 'Control de inventario')->first()?->pivot->value;
            if ($is_inventory_on) {
                $current_product = $is_global_product
                    ? GlobalProductStore::find($product_id)
                    : Product::find($product_id);

                if ($current_product->current_stock <= $product['quantity']) {
                    $current_product->update(['current_stock' => 0]);

                    // notificar si no hay existencias
                    $title = "Sin stock!";
                    $description = "Te has quedado sin existencias del producto <span class='text-primary'>$product_name</span>";
                    $url =  $is_global_product
                        ? route('global-product-store.show', base64_encode($current_product->id))
                        : route('products.show', base64_encode($current_product->id));

                    auth()->user()->notify(new BasicNotification($title, $description, $url));
                } else {
                    $current_product->decrement('current_stock', $product['quantity']);

                    // notificar si ha llegado al limite de existencias bajas
                    if ($current_product->current_stock <= $current_product->min_stock) {
                        $title = "Bajo stock!";
                        $description = "Producto <span class='text-primary'>$product_name</span> alcanzó el nivel mínimo establecido";
                        $url =  $is_global_product
                            ? route('global-product-store.show', base64_encode($current_product->id))
                            : route('products.show', base64_encode($current_product->id));
                        auth()->user()->notify(new BasicNotification($title, $description, $url));
                    }
                }
            }
        }
    }

    public function fetchCashRegisterSales($cash_register_id)
    {
        // // Obtener todas las ventas registradas y contar el número de agrupaciones por día
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

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $groupedSales = $this->getGroupedSalesByDate($sales, $online_sales)->take(30);

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
        $total_amount = $saleProducts->sum(fn ($sale) => $sale->current_price * $sale->quantity);

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
        if ($is_inventory_on) {
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
                    'historicable_id' => $current_product->id,
                    'historicable_type' => get_class($current_product),
                ]);

                // guardar id formateado y stock actual en array para enviarlo al cliente y actualizar indexedDB
                $updated_items[] = ['name' => $indexedDB_name, 'current_stock' => $current_product->current_stock];
            });
        }

        // marcar productos de venta como reembolsados / cancelados
        $saleProducts->each(fn ($sale) => $sale->update(['refunded_at' => now()]));
        // marcar status de informacion de credoto a reembolsado (si es que es a credito)
        $credit_sale_data = CreditSaleData::firstWhere('folio', $saleFolio);
        if ($credit_sale_data) {
            $credit_sale_data->update(['status' => 'Reembolsado']);
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
                if ($is_inventory_on) {
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
                            ProductHistory::create([
                                'description' => "Registro de devolución por reemplazo de producto en la venta con folio $request->folio. " . $old_quantity . ' pieza(s)',
                                'type' => 'Edición',
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
                                'description' => "Registro de venta por reemplazo del producto $current_product_name por este en la venta con folio $request->folio. " . $new_quantity . ' pieza(s)',
                                'type' => 'Edición',
                                'historicable_id' => $new_product->id,
                                'historicable_type' => get_class($new_product),
                            ]);
                        } else {
                            $current_product->increment('current_stock', $old_quantity);

                            $new_stock = $current_product->current_stock - $new_quantity;
                            $current_product->current_stock = max($new_stock, 0);
                            $current_product->save();

                            if ($old_quantity < $new_quantity) {
                                $abs_quantity = $new_quantity - $old_quantity;
                                $description = "Registro de más producto vendido por edición de la venta con folio $request->folio. " .  $abs_quantity . ' pieza(s)';
                                $type = "Edición";
                            } else {
                                $abs_quantity = $old_quantity - $new_quantity;
                                $description = "Registro de devolución de producto por edición de la venta con folio $request->folio. " .  $abs_quantity . ' pieza(s)';
                                $type = "Edición";
                            }

                            // movimiento de producto por edicion
                            ProductHistory::create([
                                'description' => $description,
                                'type' => $type,
                                'historicable_id' => $current_product->id,
                                'historicable_type' => get_class($current_product),
                            ]);
                        }
                    }
                }

                // Calcular la diferencia en montos para movimientos de caja
                $old_total = $old_quantity * $old_price;
                $new_total = $new_quantity * $new_price;
                $total_diff_amount += $new_total - $old_total;

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
            CashRegisterMovement::create([
                'amount' => abs($total_diff_amount),
                'type' => $movement_type,
                'notes' => "Actualización de venta con folio {$request->folio}",
                'cash_register_id' => $cash_register->id,
            ]);

            if ($movement_type == 'Ingreso') {
                $cash_register->increment('current_cash', abs($total_diff_amount));
            } else {
                $cash_register->decrement('current_cash', abs($total_diff_amount));
            }
        }
    }

    // private
    private function getGroupedSalesByDate($sales, $onlineSales = null, $returnSales = false)
    {
        // Combinar las ventas normales y las ventas en línea
        $allSales = collect($sales)->merge($onlineSales);

        return $allSales->groupBy(function ($sale) {
            return Carbon::parse($sale->created_at)->toDateString();
        })->map(function ($sales) use ($returnSales) {
            // Filtrar ventas normales y en línea
            $normalSales = $sales->filter(fn ($sale) => isset($sale->current_price));
            $onlineSales = $sales->filter(fn ($sale) => !isset($sale->current_price));

            $totalQuantityNormalSale = $normalSales->sum('quantity');
            $totalQuantityOnlineSale = $onlineSales->sum(function ($onlineSale) {
                return count($onlineSale->products);
            });
            $totalSale = $normalSales->sum(function ($sale) {
                return $sale->quantity * $sale->current_price;
            });

            $totalOnlineSale = $onlineSales->sum('total');

            $normalFolios = $normalSales->unique('folio')->count();
            $onlineFolios = $onlineSales->count();

            $salesByFolio = $normalSales->groupBy('folio')->map(function ($folioSales) {
                $firstSale = $folioSales->first();

                // Calcular el total de todos los productos en la venta
                $totalSale = $folioSales->sum(function ($sale) {
                    return $sale->quantity * $sale->current_price;
                });

                return [
                    'products' => $folioSales->map(function ($sale) {
                        return [
                            'id' => $sale->id,
                            'current_price' => $sale->current_price,
                            'product_name' => $sale->product_name,
                            'product_id' => $sale->product_id,
                            'is_global_product' => $sale->is_global_product,
                            'quantity' => $sale->quantity,
                            'price_changed' => $sale->price_changed,
                            'refunded_at' => $sale->refunded_at,
                            'cash_register_id' => $sale->cash_register_id,
                            'store_id' => $sale->store_id,
                            'created_at' => $sale->created_at,
                            'updated_at' => $sale->updated_at,
                        ];
                    })->values(),
                    'credit_data' => $firstSale->credit_data,
                    'folio' => $firstSale->folio,
                    'user_name' => $firstSale->user->name,
                    'total_sale' => $totalSale,
                ];
            });

            return [
                'total_normal_quantity' => $totalQuantityNormalSale,
                'total_online_quantity' => $totalQuantityOnlineSale,
                'total_sale' => $totalSale,
                'normal_folios' => $normalFolios,
                'online_folios' => $onlineFolios,
                'online_sales_total' => $totalOnlineSale,
                'sales' => $returnSales ? $salesByFolio : [],
                'online_sales' => $returnSales ? $onlineSales->values() : [],
            ];
        });
    }

    private function addCreditDataToSales($sales)
    {
        $folios = $sales->unique('folio')->pluck('folio');

        $folios->each(function ($folio) use ($sales) {
            // Buscar CreditSaleData relacionado usando el folio
            $creditData = CreditSaleData::where('folio', $folio)->first();

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
}
