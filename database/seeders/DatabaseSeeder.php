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

        \App\Models\User::factory()->create([
            'name' => 'Angel Vazquez',
            'email' => 'angel@gmail.com',
            'password' => bcrypt('321321321'),
        ]);

        Product::factory(150)->create();
        Sale::factory(1500)->create();
    }
}
