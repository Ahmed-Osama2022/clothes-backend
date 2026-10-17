<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
  /**
   * Color options with their localized names and swatch hex.
   *
   * @var array<int, array{en: string, ar: string, hex: string}>
   */
  public const COLORS = [
    ['en' => 'White', 'ar' => 'أبيض', 'hex' => '#FFFFFF'],
    ['en' => 'Black', 'ar' => 'أسود', 'hex' => '#111111'],
    ['en' => 'Beige', 'ar' => 'بيج', 'hex' => '#F5F5DC'],
    ['en' => 'Navy', 'ar' => 'كحلي', 'hex' => '#001F3F'],
  ];

  /**
   * Define the model's default state.
   *
   * `sku` is omitted on purpose — the model generates one from the product
   * slug + size + color on create.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $color = fake()->randomElement(self::COLORS);

    return [
      'product_id' => Product::factory(),
      'size' => fake()->randomElement(['XS', 'S', 'M', 'L', 'XL']),
      'color_en' => $color['en'],
      'color_ar' => $color['ar'],
      'color_hex' => $color['hex'],
      'price' => null,
      'quantity' => fake()->numberBetween(0, 20),
      'is_active' => true,
      'sort_order' => 0,
    ];
  }
}
