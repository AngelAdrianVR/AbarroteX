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
        Setting::create(['key' => 'Escanear productos', 'type' => 'Bool', 'module' => 'Punto de venta', 'description' => '']);
        Setting::create(['key' => 'Editar precio unitario', 'type' => 'Bool', 'module' => 'Punto de venta', 'description' => '']);
        Setting::create(['key' => '', 'type' => 'Bool', 'module' => 'Punto de venta', 'description' => '']);
        Setting::create(['key' => '', 'type' => 'Bool', 'module' => 'Punto de venta', 'description' => '']);
    }
}
