<?php

namespace App\Providers;

use App\Services\SerialService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //servicio para conexión de báscula con aplicación
        $this->app->singleton(SerialService::class, function ($app) {
            return new SerialService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
