<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'category_id' => Category::factory(),
      'slug' => fake()->unique()->slug(2),
      'name' => fake()->words(2, true),
      'title_en' => fake()->words(3, true),
      'title_ar' => fake()->words(3, true),
      'description_en' => fake()->sentence(),
      'description_ar' => fake()->sentence(),
      'price' => fake()->randomFloat(2, 9, 199),
      'is_featured' => fake()->boolean(20),
      'sort_order' => 0,
    ];
  }
}
