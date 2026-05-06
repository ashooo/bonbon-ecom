<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 50, 1500);

        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->optional()->paragraph(),
            'price' => $price,
            'sale_price' => fake()->boolean(30) ? fake()->randomFloat(2, 30, $price) : null,
            'unit_size' => fake()->randomElement(['box', 'pack', 'piece']),
            'is_preorder' => false,
            'preorder_days' => 0,
            'allows_customization' => false,
            'is_featured' => false,
            'is_active' => true,
            'stock_quantity' => fake()->numberBetween(5, 200),
            'main_image' => null,
            'is_best_seller' => false,
        ];
    }
}
