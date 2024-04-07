<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\Store::create([
            'name' => 'Tienda 1',
            'contact_name' => 'Contacto 1',
            'contact_phone' => '3312457896',
            'address' => 'Direccion 1',
            'plan' => 'Plan 1',
            'next_payment' => now()->addDays(10),
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Angel Vazquez',
            'email' => 'angel@gmail.com',
            'password' => bcrypt('321321321'),
            'store_id' => 1,
        ]);

        $this->call([
            SettingSeeder::class,
        ]);
        
    }
}
