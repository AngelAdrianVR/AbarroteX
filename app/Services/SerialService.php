<?php

namespace App\Services;

use App\Libraries\PhpSerial;

class SerialService
{
    protected $serial;

    public function __construct()
    {
        $this->serial = new PhpSerial();
        $this->serial->deviceSet('COM6'); // Cambia al puerto de tu báscula
        $this->serial->confBaudRate(9600);       // Configura el baudrate
    }

    public function getWeight()
    {
        $this->serial->deviceOpen();
        $data = $this->serial->readPort();
        $this->serial->deviceClose();

        return $data; // Procesa el dato si es necesario
    }
}
