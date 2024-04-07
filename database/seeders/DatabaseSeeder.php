<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
            'name' => 'Tienda 1',
            'contact_name' => 'Contacto 1',
            'contact_phone' => '3312457896',
            'address' => 'Direccion 1',
            'plan' => 'Plan 1',
            'next_payment' => now()->addDays(10),
        ]);

        // crear el primer usuario
        User::factory()->create([
            'name' => 'Angel Vazquez',
            'email' => 'angel@gmail.com',
            'password' => bcrypt('321321321'),
            'store_id' => 1,
        ]);

        // agregar mas tiendas y usuarios desde el factory
        Store::factory(10)->create();
        User::factory(20)->create();

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
