<?php

namespace App\Console\Commands;

use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSuscriptionsStatus extends Command
{
    protected $signature = 'stores:check-suscriptions';
    protected $description = 'Check suscriptions status for all stores';

    public function handle()
    {
        $stores = Store::all();

        foreach ($stores as $store) {
            $daysUntilPayment = now()->diffInDays($store->next_payment, false);

            // if ($daysUntilPayment >= 1 && $daysUntilPayment <= 3 && $store->status !== 'Próximo a vencer') {
            //     $store->update(['status' => 'Próximo a vencer']);
            // } elseif ($daysUntilPayment <= 0 && $store->status !== 'Vencido') {
            //     $store->update([
            //         'status' => 'Vencido',
            //         'is_active' => false,
            //     ]);
            // }
            if ($daysUntilPayment <= 0 && $store->status !== 'Vencido') {
                $store->update([
                    'status' => 'Vencido',
                    'is_active' => false,
                ]);
            }
        }

        Log::info('El status de las suscripciones fueron revisadas correctamente');
        $this->info('El status de las suscripciones fueron revisadas correctamente.');
    }
}
