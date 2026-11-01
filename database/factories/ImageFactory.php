<?php

namespace Database\Factories;

use Database\Seeders\ImageSeeder;
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
   * with `Image::factory()->for($category, 'imageable')`. URLs come from the
   * shared Unsplash pool in ImageSeeder so factory images are never placeholders.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'image_url' => fake()->randomElement(ImageSeeder::PHOTOS),
      'sort_order' => 0,
    ];
  }
}
