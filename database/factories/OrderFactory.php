<?php

namespace Database\Factories;

use App\Models\Order;
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
   * `user_id` is always a real user: orders are never orphaned. Use
   * `->for($user)` to attach a specific customer.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'user_id' => User::factory(),
      'status' => fake()->randomElement(Order::STATUSES),
      'total_amount' => fake()->randomFloat(2, 20, 500),
    ];
  }

  /**
   * Force a lifecycle status (handy in seeders and tests).
   */
  public function status(string $status): static
  {
    return $this->state(fn () => ['status' => $status]);
  }
}
