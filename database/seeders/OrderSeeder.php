<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
  /**
   * Seed a realistic order history: registered customers place the orders
   * (the admin does not shop), each a varying number of times, spread over
   * the past year. Older orders have naturally progressed further through the
   * lifecycle while recent ones are still in progress.
   *
   * Every order is attached to a customer (orders.user_id is NOT NULL).
   * Idempotent: a customer who already has orders is skipped, and the number
   * of orders is derived from the user id, so re-running `db:seed` is a no-op.
   */
  public function run(): void
  {
    $customers = User::query()
      ->whereHas('roles', fn ($query) => $query->where('name', 'customer'))
      ->get();

    $customers->each(function (User $user) {
      // Deterministic target (0-5) derived from the user id, so re-running the
      // seeder converges instead of piling up new orders: only the missing
      // ones are created.
      $missing = ($user->id % 6) - $user->orders()->count();

      if ($missing <= 0) {
        return;
      }

      for ($i = 0; $i < $missing; $i++) {
        $placedAt = fake()->dateTimeBetween('-12 months', 'now');
        $ageInDays = (int) now()->diffInDays($placedAt);

        Order::factory()->create([
          'user_id' => $user->id,
          'status' => $this->statusForAge($ageInDays),
          'created_at' => $placedAt,
          'updated_at' => $placedAt,
        ]);
      }
    });
  }

  /**
   * Pick a status that fits how long ago the order was placed, so the seeded
   * history reads like a real shop rather than random noise.
   */
  private function statusForAge(int $ageInDays): string
  {
    return match (true) {
      $ageInDays > 45 => fake()->randomElement(['delivered', 'delivered', 'delivered', 'cancelled']),
      $ageInDays > 14 => fake()->randomElement(['shipped', 'delivered']),
      default => fake()->randomElement(['pending', 'processing', 'shipped']),
    };
  }
}
