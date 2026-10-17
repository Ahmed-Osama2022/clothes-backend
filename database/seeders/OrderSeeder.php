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
   */
  public function run(): void
  {
    $customers = User::query()
      ->whereHas('roles', fn ($query) => $query->where('name', 'customer'))
      ->get();

    $customers->each(function (User $user) {
      // Some customers order often, some rarely, some not at all yet.
      $ordersToPlace = fake()->numberBetween(0, 5);

      for ($i = 0; $i < $ordersToPlace; $i++) {
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
