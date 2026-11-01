<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * `slug` is left out on purpose: HasSlug fills it from title_en on create.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'title_en' => fake()->unique()->sentence(3),
      'title_ar' => null,
      'intro_en' => fake()->paragraph(),
      'intro_ar' => null,
      'is_published' => true,
      'sort_order' => 0,
    ];
  }
}
