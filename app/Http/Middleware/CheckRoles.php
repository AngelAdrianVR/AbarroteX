<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoles
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user || ! in_array($user->rol, $roles)) {
            // Redirigir o mostrar un error si el usuario no tiene los roles necesarios
            return redirect('/sales-point');
        }

        return $next($request);
    }
}
