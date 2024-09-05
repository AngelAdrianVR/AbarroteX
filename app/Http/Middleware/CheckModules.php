<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModules
{
    public function handle($request, Closure $next, ...$modules)
    {
        $user = auth()->user();
        $userModules = collect($user->store->activated_modules);
        
        if (!$user || !$userModules->some($modules[0])) {
            // Redirigir o mostrar un error si la tienda no tiene el activado modulo al que quiere ingresar
            return redirect('/sales-point');
        }

        return $next($request);
    }
}
