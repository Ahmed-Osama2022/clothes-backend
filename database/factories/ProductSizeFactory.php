<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSize>
 */
class ProductSizeFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'product_id' => Product::factory(),
      'size' => fake()->randomElement(['XS', 'S', 'M', 'L', 'XL']),
      // 0 quantity = sold out; the frontend disables that size.
      'quantity' => fake()->numberBetween(0, 20),
    ];
  }
}
