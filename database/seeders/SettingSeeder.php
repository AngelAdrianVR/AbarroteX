<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create(['key' => 'Escanear productos', 'type' => 'Bool', 'module' => 'Punto de venta', 'description' => 'Mostrar barra de búsqueda por código']);
        Setting::create(['key' => 'Hacer descuentos', 'type' => 'Bool', 'module' => 'Punto de venta', 'description' => 'Poder hacer descuento sobre el monto total al registrar la venta']);

        
    }
}
