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
        Setting::create(['key' => 'Control de inventario', 'type' => 'Bool', 'module' => 'Punto de venta', 'description' => 'Se tomarán en cuenta cantidad mínima, cantidad máxima y cantidad actual de cada producto para llevar el control de inventario. En el punto de venta no se permitirá vender cantidades mayores a la cantidad actual de cada producto registrado en sistema. También se notificará al usuario cuando un producto llegue a punto de reposición y se podrán registrar entradas de producto']);
        Setting::create(['key' => 'Mostrar dinero en caja', 'type' => 'Bool', 'module' => 'Punto de venta', 'description' => 'Se muestra en la vista principal el dinero actual en caja. El dinero en caja se actualiza cada que se realiza una venta o se crea un egreso el cual fue pagado con dinero de caja']);
    }
}
