<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'order_id' => Order::factory(),
      'product_id' => Product::factory(),
      'product_variant_id' => ProductVariant::factory(),
      'quantity' => fake()->numberBetween(1, 3),
      'unit_price' => fake()->randomFloat(2, 9, 199),
    ];
  }
}
