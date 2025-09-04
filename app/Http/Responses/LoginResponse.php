<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = Auth::user();

        // dd($user->can('Ordenes de servicio'));
        if ($user->can('Ordenes de servicio')) {
            return redirect()->intended('/service-reports');
        }

        if ($user->can('Punto de venta')) {
            return redirect()->intended('/sales-point');
        }
       
        if ($user->can('Reportes')) {
            return redirect()->intended('/dashboard');
        }


    }
}
