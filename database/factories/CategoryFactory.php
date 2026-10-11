<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'slug' => fake()->unique()->slug(2),
      'name' => fake()->words(2, true),
      'title_en' => fake()->words(3, true),
      'title_ar' => fake()->words(3, true),
      'description_en' => fake()->sentence(),
      'description_ar' => fake()->sentence(),
    ];
  }
}
