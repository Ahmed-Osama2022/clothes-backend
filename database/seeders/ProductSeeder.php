<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Database\Factories\ProductVariantFactory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
  /**
   * Seed products for every category, each with a variant for every
   * color × size combination. Some quantities are 0 on purpose so the
   * frontend size selector has disabled/out-of-stock options to render.
   */
  public function run(): void
  {
    $sizes = ['XS', 'S', 'M', 'L', 'XL'];

    Category::all()->each(function (Category $category) use ($sizes) {
      Product::factory()
        ->count(6)
        ->sequence(fn ($sequence) => ['sort_order' => $sequence->index + 1])
        ->create(['category_id' => $category->id])
        ->each(function (Product $product) use ($sizes) {
          foreach (ProductVariantFactory::COLORS as $color) {
            foreach ($sizes as $size) {
              $product->variants()->create([
                'size' => $size,
                'color_en' => $color['en'],
                'color_ar' => $color['ar'],
                'color_hex' => $color['hex'],
                'quantity' => fake()->numberBetween(0, 20),
              ]);
            }
          }
        });
    });
  }
}
