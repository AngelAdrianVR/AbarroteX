<?php

namespace App\Http\Middleware;

use App\Models\OnlineSale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsOwnResource
{
    public function handle(Request $request, Closure $next): Response
    {
        $sale = OnlineSale::find($request->route('online_sale')->id);

        if (!$sale || $sale->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        return $next($request);
    }
}
