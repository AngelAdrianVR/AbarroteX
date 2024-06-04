<?php

namespace App\Console\Commands;

use App\Models\CashCut;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateCashCut extends Command
{
    protected $signature = 'store:create-cashcut';
    protected $description = 'Crear corte de caja automatico para toda caja registradora de cada tienda';

    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $stores = Store::with(['cashRegisters'])->get();
        
        foreach ($stores as $store) {
            foreach ($store->cashRegisters as $cashRegister) {
                $movementsToday = $cashRegister->movements()->whereDate('created_at', $today)->get();
                $salesToday = $cashRegister->sales()->whereDate('created_at', $today)->get();

                if ($movementsToday->isNotEmpty() || $salesToday->isNotEmpty()) {
                    // suma algebraica de todo el dinero que ingresó y salió de caja
                    $expected_cash = $cashRegister->started_cash
                                    + $movementsToday->sum('amount') 
                                    + $salesToday->sum('total');

                    // Crea el registro de corte de caja
                    CashCut::create([
                        'started_cash' => $cashRegister->started_cash,
                        'expected_cash' => $expected_cash, // suma de ventas, ingresos, retiros de caja y dinero inicial.
                        'sales_cash' => $salesToday->sum('total'),
                        'counted_cash' => $expected_cash,
                        'difference' => 0, // asume que no hay diferencias
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
