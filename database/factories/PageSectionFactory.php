<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PageSection>
 */
class PageSectionFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'page_id' => Page::factory(),
      'type' => 'text',
      'heading_en' => fake()->sentence(2),
      'heading_ar' => null,
      'body_en' => fake()->paragraph(),
      'body_ar' => null,
      'value' => null,
      'sort_order' => 0,
    ];
  }
}
