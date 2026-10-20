<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Database\Factories\ProductVariantFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
  /**
   * The storefront catalog, mirroring the frontend `src/data/catalog.js`
   * (same order, so ids stay 1–18). Names and titles are real bilingual text,
   * and descriptions are generated per category in both languages.
   *
   * @var array<int, array{category: string, name_en: string, name_ar: string, price: float}>
   */
  private const CATALOG = [
    ['category' => 'signature-collection', 'name_en' => 'Classic White Tee', 'name_ar' => 'تيشيرت أبيض كلاسيكي', 'price' => 24.99],
    ['category' => 'signature-collection', 'name_en' => 'Essential Crewneck', 'name_ar' => 'بلوفر أساسي رقبة مدورة', 'price' => 34.99],
    ['category' => 'signature-collection', 'name_en' => 'Street Co-ord', 'name_ar' => 'طقم ستريت', 'price' => 59.99],
    ['category' => 'signature-collection', 'name_en' => 'Summer Linen Shirt', 'name_ar' => 'قميص كتان صيفي', 'price' => 39.99],
    ['category' => 'signature-collection', 'name_en' => 'Classic Hoodie', 'name_ar' => 'هودي كلاسيكي', 'price' => 44.99],
    ['category' => 'signature-collection', 'name_en' => 'Urban Jacket', 'name_ar' => 'جاكيت عصري', 'price' => 69.99],
    ['category' => 'mid-season-sale', 'name_en' => 'Rack Clearance Tee', 'name_ar' => 'تيشيرت تخفيضات', 'price' => 9.99],
    ['category' => 'mid-season-sale', 'name_en' => 'Denim Day Fit', 'name_ar' => 'جينز يومي', 'price' => 44.99],
    ['category' => 'mid-season-sale', 'name_en' => 'Weekend Overshirt', 'name_ar' => 'قميص عطلة نهاية الأسبوع', 'price' => 29.99],
    ['category' => 'mid-season-sale', 'name_en' => 'Lounge Set Two-Piece', 'name_ar' => 'طقم استرخاء من قطعتين', 'price' => 19.99],
    ['category' => 'mid-season-sale', 'name_en' => 'Sale Basics 3-Pack', 'name_ar' => 'حزمة أساسيات 3 قطع', 'price' => 24.99],
    ['category' => 'mid-season-sale', 'name_en' => 'Raglan Sleeve Top', 'name_ar' => 'توب بأكمام راجلان', 'price' => 14.99],
    ['category' => 'best-sellers', 'name_en' => 'Hanging Everyday Set', 'name_ar' => 'طقم يومي معلق', 'price' => 74.99],
    ['category' => 'best-sellers', 'name_en' => 'Runway Model Top', 'name_ar' => 'توب موديل الكات ووك', 'price' => 49.99],
    ['category' => 'best-sellers', 'name_en' => 'Boutique Blazer', 'name_ar' => 'بليزر بوتيك', 'price' => 89.99],
    ['category' => 'best-sellers', 'name_en' => 'Ribbed Tank', 'name_ar' => 'تانك بضلوع', 'price' => 18.99],
    ['category' => 'best-sellers', 'name_en' => 'Studio Top', 'name_ar' => 'توب ستوديو', 'price' => 32.99],
    ['category' => 'best-sellers', 'name_en' => 'Pleated Flare Skirt', 'name_ar' => 'تنورة بليسيه واسعة', 'price' => 42.99],
  ];

  private const SIZES = ['XS', 'S', 'M', 'L', 'XL'];

  /**
   * Seed the real catalog. Each product gets one variant per color × size;
   * some quantities are 0 on purpose so the frontend size selector has
   * disabled/out-of-stock options to render.
   */
  public function run(): void
  {
    $categoryIds = Category::pluck('id', 'slug');

    foreach (self::CATALOG as $index => $item) {
      $product = Product::firstOrCreate(
        ['slug' => Str::slug($item['name_en'])],
        [
          'category_id' => $categoryIds[$item['category']],
          'name' => $item['name_en'],
          'title_en' => $item['name_en'],
          'title_ar' => $item['name_ar'],
          'description_en' => $this->descriptionEn($item['name_en'], $item['category']),
          'description_ar' => $this->descriptionAr($item['name_ar'], $item['category']),
          'price' => $item['price'],
          'is_featured' => $index % 6 === 0,
          'sort_order' => ($index % 6) + 1,
        ]
      );

      // Only build variants for products created on this run, so re-seeding
      // does not duplicate rows (the unique size/color index would reject them).
      if (! $product->wasRecentlyCreated) {
        continue;
      }

      foreach (ProductVariantFactory::COLORS as $color) {
        foreach (self::SIZES as $size) {
          $product->variants()->create([
            'size' => $size,
            'color_en' => $color['en'],
            'color_ar' => $color['ar'],
            'color_hex' => $color['hex'],
            'quantity' => fake()->numberBetween(0, 20),
          ]);
        }
      }
    }
  }

  /**
   * English marketing copy, varied by category.
   */
  private function descriptionEn(string $name, string $category): string
  {
    return match ($category) {
      'signature-collection' => "{$name} is cut from soft, breathable fabric for effortless everyday wear. A versatile staple designed to move with you, season after season.",
      'mid-season-sale' => "{$name}, part of our mid-season sale. A seasonal favourite at a limited-time price — crafted for comfort and made to last.",
      'best-sellers' => "{$name}, one of our best sellers. Loved for its comfortable fit and easy styling, it's a piece you'll reach for again and again.",
      default => $name,
    };
  }

  /**
   * Arabic marketing copy, varied by category.
   */
  private function descriptionAr(string $name, string $category): string
  {
    return match ($category) {
      'signature-collection' => "{$name} مصنوع من قماش ناعم يسمح بمرور الهواء لراحة يومية. قطعة أساسية متعددة الاستخدامات صُممت لتتحرك معك موسماً بعد موسم.",
      'mid-season-sale' => "{$name} ضمن تخفيضات منتصف الموسم. من مفضلات الموسم بسعر لفترة محدودة — مصنوع للراحة ويدوم طويلاً.",
      'best-sellers' => "{$name} من الأكثر مبيعاً لدينا. محبوب لراحته وسهولة تنسيقه، قطعة ستعود إليها مراراً وتكراراً.",
      default => $name,
    };
  }
}
