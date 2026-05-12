<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variant>
 */
class VariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => fake()->optional()->words(2, true),
            'sku' => strtoupper('SKU-' . Str::random(10)),
            'price_adjustment' => fake()->randomFloat(2, -20, 120),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'is_default' => false,
            'display_order' => fake()->numberBetween(0, 5),
            'is_active' => true,
        ];
    }
}
