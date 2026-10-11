<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
  /**
   * Seed the roles the app relies on by name. Idempotent so re-seeding does
   * not create duplicates.
   */
  public function run(): void
  {
    foreach (['admin', 'customer'] as $name) {
      Role::firstOrCreate(['name' => $name]);
    }
  }
}
