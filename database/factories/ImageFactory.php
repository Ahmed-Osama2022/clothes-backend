<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * The polymorphic owner is intentionally left out; attach it at call time
   * with `Image::factory()->for($category, 'imageable')`.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'url' => fake()->imageUrl(800, 800, 'fashion'),
      'sort_order' => 0,
    ];
  }
}
