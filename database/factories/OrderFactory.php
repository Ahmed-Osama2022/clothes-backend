<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      // Pass `['user_id' => null]` for a guest order.
      'user_id' => User::factory(),
      'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
      'total_amount' => fake()->randomFloat(2, 20, 500),
    ];
  }
}
