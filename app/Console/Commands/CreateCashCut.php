<?php

namespace App\Console\Commands;

use App\Models\CashCut;
use App\Models\OnlineSale;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateCashCut extends Command
{
    protected $signature = 'stores:create-cashcut';
    protected $description = 'Crear corte de caja automatico para toda caja registradora de cada tienda';

    public function handle()
    {
        $stores = Store::with(['cashRegisters'])->get(['id', 'name', 'online_store_properties']);
        
        foreach ($stores as $store) {
            foreach ($store->cashRegisters as $cashRegister) {
                //recupera el último corte
                $last_cash_cut = CashCut::where('cash_register_id', $cashRegister->id)->latest()->first();

                //recupera el id de la caja registradora para ventas en linea
                $online_cash_register_id = $store->online_store_properties['online_sales_cash_register'];
                if ( $online_cash_register_id == $cashRegister->id ) { // si la caja registradora es la misma que la registrada para ventas en línea entonces lo toma en cuenta
                    // si existe el ultimo corte
                    if (  $last_cash_cut != null ) {
                        $onlineSalesPendentToday = OnlineSale::where('store_id', auth()->user()->store_id)
                                        ->where('status', 'Entregado')
                                        ->where('created_at', '>', $last_cash_cut->created_at)
                                        ->latest()
                                        ->get();
                    } else { //si no existe ningun corte, recupera todas las ventas en linea
                        $onlineSalesPendentToday = OnlineSale::where('store_id', auth()->user()->store_id)
                                        ->where('status', 'Entregado')
                                        ->latest()
                                        ->get();
                    }
                    
                }

                //movimientos pendientes realizados despues del ultimo corte
                if ( $last_cash_cut != null ) {
                    $movementsPendentToday = $cashRegister->movements()->where('created_at', '>', $last_cash_cut->created_at)->latest()->get();
                } else {
                    $movementsPendentToday = $cashRegister->movements()->latest()->get();
                }

                //Ventas pendientes realizadas despues del ultimo corte
                if ( $last_cash_cut != null ) {
                    $salesPendentToday = $cashRegister->sales()->where('created_at', '>', $last_cash_cut->created_at)->latest()->get();
                } else {
                    $salesPendentToday = $cashRegister->sales()->latest()->get();
                }
                                
                
                //si hay movimientos y/o ventas pendientes
                if ($movementsPendentToday->isNotEmpty() || $salesPendentToday->isNotEmpty() || $onlineSalesPendentToday->isNotEmpty()) {

                    // suma algebraica de todo el dinero que ingresó y salió de caja
                    $expected_cash = $cashRegister->started_cash //dinero inicial en caja
                        + $movementsPendentToday->where('type', 'Ingreso')->sum('amount') //movimientos de ingreso
                        - $movementsPendentToday->where('type', 'Retiro')->sum('amount') //movimientos de retiro
                        + $totalOnlineSaleToday = $onlineSalesPendentToday->sum(function($online_sale) { //ventas en línea
                            return $online_sale->total;
                            });
                        + $totalStoreSalesToday = $salesPendentToday->sum(function($sale) { //ventas en tienda
                            return $sale->quantity * $sale->current_price;
                            });
                    
                    // Crea el registro de corte de caja
                    CashCut::create([
                        'started_cash' => $cashRegister->started_cash,
                        'expected_cash' => $expected_cash, // suma de ventas, ingresos, retiros de caja y dinero inicial.
                        'store_sales_cash' => $totalStoreSalesToday, //ventas en tienda
                        'online_sales_cash' => $totalOnlineSaleToday, //ventas en línea
                        'counted_cash' => $expected_cash,
                        'difference' => 0, // asume que no hay diferencias
                        'withdrawn_cash' => 0,
                        'notes' => 'Corte realizado automáticamente por el sistema al terminar el día',
                        'cash_register_id' => $cashRegister->id,
                        'store_id' => $store->id,
                        'user_id' => 1, // Id del usuario que ejecuta el comando, se puede modificar según la lógica del sistema
                    ]);

                    // se asigna el dinero contado al dinero inicial de caja registradora para el próximo corte 
                    $cashRegister->started_cash = $expected_cash;
                    $cashRegister->current_cash = $expected_cash;
                    $cashRegister->save();

                }
            }
        }

        Log::info('Se crearon los cortes para cada caja');
        $this->info('Se crearon los cortes para cada caja.');
    }
}
