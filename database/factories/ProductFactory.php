<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
        return [
            'title' => fake()->sentence(),
            'slug' => fake()->slug(),
            'description' => fake()->text(),
            'price' => fake()->randomFloat(2, 200, 1000),
            'stock' => fake()->numberBetween(0, 100),
            'thumbnail' => "https://www.vecteezy.com/free-photos/product-placeholder",
            'discount_price' => fake()->randomFloat(2, 0, 800),
            'brand_id' => fn()=>Brand::factory()->create(),
            'user_id' => fn()=>User::factory()->create(),
        ];
    }
}
