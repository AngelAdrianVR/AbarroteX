<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $min_stock = $this->faker->numberBetween(0, 100);
        $max_stock = $min_stock + 10;
        $cost = $this->faker->numberBetween(3, 85);

        return [
            'name' => $this->faker->text(50),
            'public_price' => $cost * 1.10,
            'cost' => $cost,
            'code' => $this->faker->numberBetween(0, 200),
            'min_stock' => $min_stock,
            'max_stock' => $max_stock,
            'current_stock' => $this->faker->numberBetween($min_stock, $max_stock),
        ];
    }
}
