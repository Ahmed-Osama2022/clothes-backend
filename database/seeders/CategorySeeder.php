<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
  /**
   * Seed the three categories the frontend routes depend on. The slugs must
   * match the frontend exactly (handoff §5 / §8), so they are hardcoded and
   * upserted by slug to stay idempotent across re-seeds.
   */
  public function run(): void
  {
    $categories = [
      [
        'slug' => 'signature-collection',
        'name' => 'Signature Collection',
        'title_en' => 'Signature Collection',
        'title_ar' => 'المجموعة المميزة',
        'description_en' => 'Timeless essentials crafted to last.',
        'description_ar' => 'أساسيات خالدة صُنعت لتدوم.',
      ],
      [
        'slug' => 'mid-season-sale',
        'name' => 'Mid Season Sale',
        'title_en' => 'Mid Season Sale',
        'title_ar' => 'تخفيضات منتصف الموسم',
        'description_en' => 'Limited-time markdowns on selected pieces.',
        'description_ar' => 'تخفيضات لفترة محدودة على قطع مختارة.',
      ],
      [
        'slug' => 'best-sellers',
        'name' => 'Best Sellers',
        'title_en' => 'Best Sellers',
        'title_ar' => 'الأكثر مبيعاً',
        'description_en' => 'The pieces our customers love most.',
        'description_ar' => 'القطع الأكثر محبة من عملائنا.',
      ],
    ];

    foreach ($categories as $category) {
      Category::updateOrCreate(['slug' => $category['slug']], $category);
    }
  }
}
