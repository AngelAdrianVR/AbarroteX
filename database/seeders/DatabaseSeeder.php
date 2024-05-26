<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Admin;
use App\Models\CashRegister;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         // crear las configuraciones
         $this->call([
            SettingSeeder::class,
        ]);

        // crear la primera tienda
        Store::create([
            'name' => 'Tienda de pruebas',
            'contact_name' => 'Administrador',
            'contact_phone' => '3312155731',
            'address' => '---',
            'type' => 'Abarrotes',
            'next_payment' => now()->addDays(15),
            'default_card_id' => 1,
        ]);

        // crear la primera caja registradora de la tienda
        CashRegister::create([
            'name' => 'Caja 1',
            'started_cash' => 0,
            'current_cash' => 0,
            'max_cash' => 5000,
            'store_id' => 1,
        ]);

        // crear el primer usuario
        User::factory()->create([
            'name' => 'Angel Vazquez',
            'email' => 'angel@gmail.com',
            'rol' => 'Administrador',
            'password' => bcrypt('321321321'),
            'store_id' => 1,
            'rol' => 'Administrador',
        ]);
        
        // crear el primer vendedor
        Admin::create([
            'name' => 'Miguel Vazquez',
            'email' => 'miguel@gmail.com',
            'password' => bcrypt('321321321'),
            'phone' => 3333034738,
        ]);

        // agregar mas tiendas y usuarios desde el factory
        // Store::factory(10)->create();
        // User::factory(20)->create();

        // agregar las configuraciones iniciales a las tiendas
        $stores = Store::all();
        $settings = Setting::all();
        $stores->each(function($store) use ($settings) {
            $settings->each(function($setting) use ($store){
                $store->settings()->attach($setting->id, ['value' => 1]);
            });
        });
    }
}
