<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
  /**
   * Attach polymorphic images to every category and product. saveMany()
   * fills the imageable_type / imageable_id pair automatically.
   */
  public function run(): void
  {
    Category::all()->each(function (Category $category) {
      $category->images()->saveMany(Image::factory()->count(2)->make());
    });

    Product::all()->each(function (Product $product) {
      $product->images()->saveMany(Image::factory()->count(3)->make());
    });
  }
}
