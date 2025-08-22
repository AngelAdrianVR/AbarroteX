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

    public function handle()
    {
        $stores = Store::with(['cashRegisters', 'users'])->get(['id', 'name', 'online_store_properties']);

        foreach ($stores as $store) {
            if (
                empty($store->online_store_properties) ||
                !array_key_exists('online_sales_cash_register', $store->online_store_properties)
            ) {
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

                // $serviceOrderQuery = ServiceReport::where('store_id', $store->id)
                //     ->where('status', 'Entregado/Pagado'); // no se toman en cuenta las canceladas pero revisar si se tienen que tomar en cuenta

                if ($last_cash_cut) {
                    // ---- Se buscan solo órdenes de servicio ya liquidadas ----
                    $serviceOrdersPendentToday = ServiceReport::where('store_id', $store->id)
                        ->where('status', 'Entregado/Pagado') // Solo las completadas
                        ->where('paid_at', '>', $last_cash_cut->created_at)
                        ->get();

                    // ---- Obtener anticipos a partir del ultimo corte realizado ----
                    $today_advances = ServiceReport::where('store_id', $store->id)
                        // CAMBIO: Se usa where() para comparar fecha y hora completas
                        ->where('created_at', '>', $last_cash_cut->created_at)
                        // ->where('status', '!=', 'Entregado/Pagado') // Solo las que no están completadas
                        ->whereIn('payment_method', ['Tarjeta', 'Transferencia']) // Solo con estos métodos
                        ->sum('advance_payment'); // Suma solo el campo 'advance_payment'

                    $today_advances_cash = ServiceReport::where('store_id', $store->id)
                        // CAMBIO: Se usa where() para comparar fecha y hora completas
                        ->where('created_at', '>', $last_cash_cut->created_at)
                        // ->where('status', '!=', 'Entregado/Pagado') // Solo las que no están completadas
                        ->whereIn('payment_method', ['Efectivo']) // Solo con estos métodos
                        ->sum('advance_payment'); // Suma solo el campo 'advance_payment'
                } else {
                    // ---- Se buscan solo órdenes de servicio ya liquidadas ----
                    $serviceOrdersPendentToday = ServiceReport::where('store_id', $store->id)
                        ->where('status', 'Entregado/Pagado') // Solo las completadas
                        ->get();

                    // ---- Obtener todos los anticipos  ----
                    $today_advances = ServiceReport::where('store_id', $store->id)
                        // ->where('status', '!=', 'Entregado/Pagado') // Solo las que no están completadas
                        ->whereIn('payment_method', ['Tarjeta', 'Transferencia']) // Solo con estos métodos
                        ->sum('advance_payment'); // Suma solo el campo 'advance_payment'
                    $today_advances_cash = ServiceReport::where('store_id', $store->id)
                        // ->where('status', '!=', 'Entregado/Pagado') // Solo las que no están completadas
                        ->whereIn('payment_method', ['Efectivo']) // Solo con estos métodos
                        ->sum('advance_payment'); // Suma solo el campo 'advance_payment'
                }

                // $serviceOrdersPendentToday = $serviceOrderQuery->get();

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
                // $service_orders_cash = $serviceOrdersPendentToday->where('payment_method', 'Efectivo')->sum('service_cost');
                // $service_orders_card = $serviceOrdersPendentToday->where('payment_method', 'Tarjeta')->sum('service_cost');

                // ---- Se calcula la liquidación de las órdenes (Total - Anticipo) ----
                $service_cash_settlement = $serviceOrdersPendentToday->where('payment_method', 'Efectivo')
                    ->sum(fn($order) => $order->service_cost - $order->advance_payment);
                $service_card_settlement = $serviceOrdersPendentToday->where('payment_method', 'Tarjeta')
                    ->sum(fn($order) => $order->service_cost - $order->advance_payment);

                // Totales esperados
                $expected_cash = $cashRegister->started_cash
                    + $movementsPendentToday->where('type', 'Ingreso')->sum('amount')
                    - $movementsPendentToday->where('type', 'Retiro')->sum('amount')
                    + $store_sales_cash
                    + $online_sales_cash
                    + $service_cash_settlement
                    + $today_advances_cash;

                $expected_card = $store_sales_card
                    + $online_sales_card
                    + $service_card_settlement
                    + $today_advances;

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
                        'service_orders_cash' => $service_cash_settlement,
                        'service_orders_card' => $service_card_settlement,
                        'service_orders_advance_cash' => $today_advances_cash,
                        'service_orders_advance_card' => $today_advances,
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
