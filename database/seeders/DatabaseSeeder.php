<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * Order matters: roles first (users attach to them), then users, then the
   * content chain categories -> products (+ sizes) -> images -> orders -> items.
   */
  public function run(): void
  {
    $this->call(RoleSeeder::class);

    $admin = Role::where('name', 'admin')->firstOrFail();
    $customer = Role::where('name', 'customer')->firstOrFail();

    // One admin account for the dashboard side.
    User::factory()
      ->create(['name' => 'Admin', 'email' => 'admin@example.com'])
      ->roles()->attach($admin);

    // Registered shoppers.
    User::factory(10)->create()->each(
      fn (User $user) => $user->roles()->attach($customer)
    );

    User::factory()
      ->create(['name' => 'Test User', 'email' => 'test@example.com'])
      ->roles()->attach($customer);

    $this->call([
      CategorySeeder::class,
      ProductSeeder::class,
      ImageSeeder::class,
      OrderSeeder::class,
      OrderItemSeeder::class,
    ]);
  }
}
