<?php

namespace App\Console\Commands;

use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateCashCut extends Command
{
    protected $signature = 'store:create-cashcut';
    protected $description = 'Crear corte de caja automatico para toda caja registradora de cada tienda';

    public function handle()
    {
        $stores = Store::all();
        
        foreach ($stores as $store) {
            // aqui logica para crear los cortes en cada caja registora de la tienda simpre y cuando hayan habido movimientos
            //--
        }

        Log::info('Se crearon los cortes para cada caja');
        $this->info('Se crearon los cortes para cada caja.');
    }
}
