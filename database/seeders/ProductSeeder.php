<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
  /**
   * Seed products for every category, each with one product_sizes row per
   * XS..XL. Some sizes are seeded with quantity 0 on purpose so the frontend
   * size selector has disabled/out-of-stock options to render.
   */
  public function run(): void
  {
    $sizes = ['XS', 'S', 'M', 'L', 'XL'];

    Category::all()->each(function (Category $category) use ($sizes) {
      Product::factory()
        ->count(6)
        ->create(['category_id' => $category->id])
        ->each(function (Product $product) use ($sizes) {
          foreach ($sizes as $size) {
            $product->sizes()->create([
              'size' => $size,
              'quantity' => fake()->numberBetween(0, 20),
            ]);
          }
        });
    });
  }
}
