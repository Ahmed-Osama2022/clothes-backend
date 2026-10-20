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
   * Coherent bilingual samples so factory-made products get real English and
   * Arabic text instead of English faker words copied into the Arabic columns.
   *
   * @var array<int, array{en: array{name: string, description: string}, ar: array{name: string, description: string}}>
   */
  private const SAMPLES = [
    [
      'en' => ['name' => 'Classic White Tee', 'description' => 'A soft cotton staple cut for everyday wear. Versatile, breathable and easy to style.'],
      'ar' => ['name' => 'تيشيرت أبيض كلاسيكي', 'description' => 'قطعة قطنية ناعمة مصممة للارتداء اليومي. بسيطة ومتعددة الاستخدامات وسهلة التنسيق.'],
    ],
    [
      'en' => ['name' => 'Essential Crewneck', 'description' => 'A relaxed crewneck jumper with a clean finish — the layer you reach for all year round.'],
      'ar' => ['name' => 'بلوفر أساسي رقبة مدورة', 'description' => 'بلوفر برقبة مدورة بقصة مريحة وتشطيب أنيق — الطبقة التي تعود إليها طوال العام.'],
    ],
    [
      'en' => ['name' => 'Summer Linen Shirt', 'description' => 'Lightweight linen with a relaxed drape, made for warm days and easy evenings.'],
      'ar' => ['name' => 'قميص كتان صيفي', 'description' => 'كتان خفيف بقصة مريحة، صُنع للأيام الدافئة والأمسيات الهادئة.'],
    ],
    [
      'en' => ['name' => 'Classic Hoodie', 'description' => 'Brushed-back fleece and a roomy hood keep this everyday hoodie cosy and comforting.'],
      'ar' => ['name' => 'هودي كلاسيكي', 'description' => 'قطن مبطن بظهر ناعم وقلنسوة واسعة يمنحان هذا الهودي اليومي دفئاً وراحة.'],
    ],
    [
      'en' => ['name' => 'Boutique Blazer', 'description' => 'A tailored blazer that sharpens any outfit without feeling stiff.'],
      'ar' => ['name' => 'بليزر بوتيك', 'description' => 'بليزر بقصة خياطة يمنح إطلالتك أناقة دون أن يبدو متصلباً.'],
    ],
    [
      'en' => ['name' => 'Pleated Flare Skirt', 'description' => 'Fluid pleats and a flattering flare that moves beautifully with every step.'],
      'ar' => ['name' => 'تنورة بليسيه واسعة', 'description' => 'طيات انسيابية وقصة واسعة تتحرك بجمال مع كل خطوة.'],
    ],
  ];

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $sample = fake()->randomElement(self::SAMPLES);

    return [
      'category_id' => Category::factory(),
      'name' => $sample['en']['name'],
      'title_en' => $sample['en']['name'],
      'title_ar' => $sample['ar']['name'],
      'description_en' => $sample['en']['description'],
      'description_ar' => $sample['ar']['description'],
      'price' => fake()->randomFloat(2, 9, 199),
      'is_featured' => fake()->boolean(20),
      'sort_order' => fake()->numberBetween(0, 20),
    ];
  }
}
