<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\PhpSerial;
use App\Models\User;

class ScaleController extends Controller
{
    // Obtiene los puertos seriales disponibles de la computadora
    public function getAvailablePorts()
    {
        $ports = []; // Para Windows, puedes usar el comando mode
        $output = shell_exec('mode'); // Ejecuta el comando
        preg_match_all('/COM\d+/', $output, $matches); // Encuentra los puertos COM
        $ports = $matches[0] ?? []; // Guarda los puertos encontrados

        return response()->json($ports);
    }

    // Guarda la configuración de la báscula en la base de datos
    public function configure(Request $request, User $user)
    {
        $user->update($request->all());
    }

}

