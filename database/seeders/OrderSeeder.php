<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
  /**
   * Seed a couple of orders for the existing users, plus guest orders
   * (user_id null) since guests are allowed to check out.
   */
  public function run(): void
  {
    User::query()->take(5)->get()->each(function (User $user) {
      Order::factory()->count(2)->create(['user_id' => $user->id]);
    });

    Order::factory()->count(2)->create(['user_id' => null]);
  }
}
