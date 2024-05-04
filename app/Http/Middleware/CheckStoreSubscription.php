<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $store = auth()->user()->store;

        // Obtener la diferencia en días de la fecha de siguiente pago a hoy
        $days = now()->diffInDays($store->next_payment, false);

        // revisar tambien dias de atraso en el pago por si falla comando para actualizar actividad de las cuentas
        if (!$store->is_active || $days <= 0) {
            return redirect()->route('profile.show'); // Redirige a la ruta de pago de suscripción
        }

        return $next($request);
    }
}
