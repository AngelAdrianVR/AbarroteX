<?php

namespace App\Console\Commands;

use App\Models\CashCut;
use App\Models\CreditSaleData;
use App\Models\OnlineSale;
use App\Models\ServiceReport;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateCashCut extends Command
{
    protected $signature = 'stores:create-cashcut';
    protected $description = 'Crear corte de caja automatico para toda caja registradora de cada tienda';

    // ! El metodo esta comentado porque se agregó ordenes de servicio todas las ventas se separaron por metodo de pago. Abajo está el nuevo método.
    // ! Si funciona se puede borrar este

    // public function handle()
    // {
    //     $stores = Store::with(['cashRegisters'])->get(['id', 'name', 'online_store_properties']);
        
    //     foreach ($stores as $store) {
    //         // validación previa para asegurarte que todas las tiendas tienen correctamente configuradas sus propiedades, o registrar en logs las que no lo tienen
    //         if (empty($store->online_store_properties) || 
    //             !array_key_exists('online_sales_cash_register', $store->online_store_properties)) {
    //             Log::warning("Tienda {$store->id} no tiene configurada la propiedad 'online_sales_cash_register'");
    //             continue;
    //         }

    //         // Si no tiene cajas registradoras, no hace nada
    //         if ($store->cashRegisters->isEmpty()) {
    //             Log::warning("Tienda {$store->id} no tiene cajas registradoras configuradas.");
    //             continue;
    //         }

    //         // Recorre cada caja registradora de la tienda
    //         foreach ($store->cashRegisters as $cashRegister) {
    //             //recupera el último corte
    //             $last_cash_cut = CashCut::where('cash_register_id', $cashRegister->id)->latest()->first();

    //             $onlineSalesPendentToday = collect();
    //             if ($store->online_store_properties != []) {
    //                 //recupera el id de la caja registradora para ventas en linea
    //                 // $online_cash_register_id = $store->online_store_properties['online_sales_cash_register']; // forma antigua de acceder a la propiedad
    //                 $online_cash_register_id = data_get($store->online_store_properties, 'online_sales_cash_register');

    //                 if ($online_cash_register_id && $online_cash_register_id == $cashRegister->id) {
    //                     // si existe el ultimo corte
    //                     if ($last_cash_cut != null) {
                            
    //                         $onlineSalesPendentToday = OnlineSale::where('store_id', $store->id)
    //                             ->where('status', 'Entregado')
    //                             ->where('created_at', '>', $last_cash_cut->created_at)
    //                             ->latest()
    //                             ->get();
    //                     } else { //si no existe ningun corte, recupera todas las ventas en linea
    //                         $onlineSalesPendentToday = OnlineSale::where('store_id', $store->id)
    //                             ->where('status', 'Entregado')
    //                             ->latest()
    //                             ->get();
    //                     }
    //                 }
    //             }

    //             //movimientos pendientes realizados despues del ultimo corte
    //             if ($last_cash_cut != null) {
    //                 $movementsPendentToday = $cashRegister->movements()->where('created_at', '>', $last_cash_cut->created_at)->latest()->get();
    //             } else {
    //                 $movementsPendentToday = $cashRegister->movements()->latest()->get();
    //             }

    //             //Ventas en tienda pendientes realizadas despues del ultimo corte
    //             if ($last_cash_cut != null) {
    //                 $salesPendentToday = $cashRegister->sales()
    //                     ->where('created_at', '>', $last_cash_cut->created_at)->where('payment_method', 'Efectivo')
    //                     ->latest()
    //                     ->get();
    //             } else {
    //                 $salesPendentToday = $cashRegister->sales()->latest()->get();
    //             }

    //             //calcula el total de ventas en tienda
    //             $totalStoreSalesToday = $salesPendentToday->sum(function ($sale) { //ventas en tienda
    //                 return $sale->quantity * $sale->current_price;
    //             });
                
    //             //calcula el total de ventas en línea
    //             $totalOnlineSaleToday = $onlineSalesPendentToday->sum(function ($online_sale) { //ventas en línea
    //                 return $online_sale->total;
    //             });

    //             //si hay movimientos y/o ventas pendientes
    //             if ($movementsPendentToday->isNotEmpty() || $salesPendentToday->isNotEmpty() || $onlineSalesPendentToday->isNotEmpty()) {
    //                 // suma algebraica de todo el dinero que ingresó y salió de caja
    //                 $expected_cash = $cashRegister->started_cash 
    //                 + $movementsPendentToday->where('type', 'Ingreso')->sum('amount') 
    //                 - $movementsPendentToday->where('type', 'Retiro')->sum('amount') 
    //                 + $totalStoreSalesToday 
    //                 + $totalOnlineSaleToday;

    //                 // Asegurarse de que el valor no sea negativo
    //                 if ($expected_cash < 0) {
    //                     $expected_cash = 0;
    //                 }

    //                 // Crea el registro de corte de caja
    //                 CashCut::create([
    //                     'started_cash' => $cashRegister->started_cash,
    //                     'expected_cash' => $expected_cash, // suma de ventas, ingresos, retiros de caja y dinero inicial.
    //                     'store_sales_cash' => $totalStoreSalesToday, //ventas en tienda
    //                     'online_sales_cash' => $totalOnlineSaleToday, //ventas en línea
    //                     'counted_cash' => $expected_cash,
    //                     'difference' => 0, // asume que no hay diferencias
    //                     'withdrawn_cash' => 0, //no se retira nada
    //                     'notes' => 'Corte realizado automáticamente por el sistema al terminar el día',
    //                     'cash_register_id' => $cashRegister->id,
    //                     'store_id' => $store->id,
    //                     'user_id' => $store->users->first()->id, // Id del usuario que ejecuta el comando, se puede modificar según la lógica del sistema
    //                 ]);

    //                 // se asigna el dinero contado al dinero inicial de caja registradora para el próximo corte 
    //                 $cashRegister->started_cash = $expected_cash;
    //                 $cashRegister->current_cash = $expected_cash;
    //                 $cashRegister->save();
    //             }
    //         }
    //     }

    //     Log::info('Se crearon los cortes para cada caja');
    //     $this->info('Se crearon los cortes para cada caja.');
    // }

    public function handle()
    {
        $stores = Store::with(['cashRegisters', 'users'])->get(['id', 'name', 'online_store_properties']);

        foreach ($stores as $store) {
            if (empty($store->online_store_properties) ||
                !array_key_exists('online_sales_cash_register', $store->online_store_properties)) {
                Log::warning("Tienda {$store->id} no tiene configurada la propiedad 'online_sales_cash_register'");
                continue;
            }

            if ($store->cashRegisters->isEmpty()) {
                Log::warning("Tienda {$store->id} no tiene cajas registradoras configuradas.");
                continue;
            }

            foreach ($store->cashRegisters as $cashRegister) {
                $last_cash_cut = CashCut::where('cash_register_id', $cashRegister->id)->latest()->first();

                $onlineSalesPendentToday = collect();
                $serviceOrdersPendentToday = collect();

                $online_cash_register_id = data_get($store->online_store_properties, 'online_sales_cash_register');

                if ($online_cash_register_id && $online_cash_register_id == $cashRegister->id) {
                    $onlineSalesQuery = OnlineSale::where('store_id', $store->id)
                        ->whereIn('status', ['Entregado', 'Reembolsado']);

                    if ($last_cash_cut) {
                        $onlineSalesQuery->where('delivered_at', '>', $last_cash_cut->created_at);
                    }

                    $onlineSalesPendentToday = $onlineSalesQuery->get();
                }

                $serviceOrderQuery = ServiceReport::where('store_id', $store->id)
                    ->where('status', 'Entregado/Pagado'); // no se toman en cuenta las canceladas pero revisar si se tienen que tomar en cuenta

                if ($last_cash_cut) {
                    $serviceOrderQuery->where('paid_at', '>', $last_cash_cut->created_at);
                }

                $serviceOrdersPendentToday = $serviceOrderQuery->get();

                $movementsPendentToday = $last_cash_cut
                    ? $cashRegister->movements()->where('created_at', '>', $last_cash_cut->created_at)->latest()->get()
                    : $cashRegister->movements()->latest()->get();

                $salesPendentToday = $last_cash_cut
                    ? $cashRegister->sales()->where('created_at', '>', $last_cash_cut->created_at)->latest()->get()
                    : $cashRegister->sales()->latest()->get();

                // Filtra ventas a crédito
                $credit_folios = CreditSaleData::where('store_id', $store->id)->pluck('folio')->toArray();
                $filtered_sales = $salesPendentToday->reject(fn($sale) => in_array($sale->folio, $credit_folios));

                // Ventas en tienda
                $store_sales_cash = $filtered_sales->where('payment_method', 'Efectivo')->sum(fn($s) => $s->quantity * $s->current_price);
                $store_sales_card = $filtered_sales->where('payment_method', 'Tarjeta')->sum(fn($s) => $s->quantity * $s->current_price);

                // Ventas en línea
                $online_sales_cash = $onlineSalesPendentToday->where('payment_method', 'Efectivo')->sum(fn($o) => $o->total + $o->delivery_price);
                $online_sales_card = $onlineSalesPendentToday->where('payment_method', 'Tarjeta')->sum(fn($o) => $o->total + $o->delivery_price);

                // Órdenes de servicio
                $service_orders_cash = $serviceOrdersPendentToday->where('payment_method', 'Efectivo')->sum('service_cost');
                $service_orders_card = $serviceOrdersPendentToday->where('payment_method', 'Tarjeta')->sum('service_cost');

                // Totales esperados
                $expected_cash = $cashRegister->started_cash
                    + $movementsPendentToday->where('type', 'Ingreso')->sum('amount')
                    - $movementsPendentToday->where('type', 'Retiro')->sum('amount')
                    + $store_sales_cash
                    + $online_sales_cash
                    + $service_orders_cash;

                $expected_card = $store_sales_card
                    + $online_sales_card
                    + $service_orders_card;

                if (
                    $movementsPendentToday->isNotEmpty() ||
                    $filtered_sales->isNotEmpty() ||
                    $onlineSalesPendentToday->isNotEmpty() ||
                    $serviceOrdersPendentToday->isNotEmpty()
                ) {
                    CashCut::create([
                        'started_cash' => $cashRegister->started_cash,
                        // 'started_card' => $cashRegister->started_card,
                        'expected_cash' => max($expected_cash, 0),
                        'expected_card' => max($expected_card, 0),
                        'store_sales_cash' => $store_sales_cash,
                        'store_sales_card' => $store_sales_card,
                        'online_sales_cash' => $online_sales_cash,
                        'online_sales_card' => $online_sales_card,
                        'service_orders_cash' => $service_orders_cash,
                        'service_orders_card' => $service_orders_card,
                        'counted_cash' => max($expected_cash, 0),
                        'counted_card' => max($expected_card, 0),
                        'withdrawn_cash' => 0,
                        'withdrawn_card' => 0,
                        'difference_cash' => 0,
                        'difference_card' => 0,
                        'notes' => 'Corte realizado automáticamente por el sistema al terminar el día',
                        'cash_register_id' => $cashRegister->id,
                        'store_id' => $store->id,
                        'user_id' => $store->users->first()?->id,
                    ]);

                    $cashRegister->started_cash = max($expected_cash, 0);
                    $cashRegister->current_cash = max($expected_cash, 0);
                    // $cashRegister->started_card = max($expected_card, 0);
                    // $cashRegister->current_card = max($expected_card, 0);
                    $cashRegister->save();
                }
            }
        }

        Log::info('Se crearon los cortes para cada caja');
        $this->info('Se crearon los cortes para cada caja.');
    }

}
