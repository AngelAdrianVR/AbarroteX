<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\PhpSerial;
use App\Models\User;

class ScaleController extends Controller
{
    protected $serial;

    public function __construct()
    {
        $this->serial = new PhpSerial();
    }

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

    // Lee el peso desde la báscula
    public function readWeight()
    {   
        $user = User::find(auth()->id());

        $port = $user->scale_config['port'];
        $baudRate = (int) $user->scale_config['baudRate'];
        $parity = $user->scale_config['parity']; // none, odd, even
        $dataBits = (int) $user->scale_config['dataBit'];
        $stopBits = (int) $user->scale_config['stopBit'];
        $flowControl = $user->scale_config['flowControl']; // none, xon/xoff, rts/cts

        $this->serial->deviceSet($port);
        $this->serial->confBaudRate($baudRate);
        $this->serial->confParity($parity);
        $this->serial->confCharacterLength($dataBits);
        $this->serial->confStopBits($stopBits);
        $this->serial->confFlowControl($flowControl);

        $this->serial->deviceOpen();
        sleep(1); // Esperar un segundo para capturar datos
        $data = $this->serial->readPort();
        $this->serial->deviceClose();

        return response()->json(['weight' => $data]);

    }
}

