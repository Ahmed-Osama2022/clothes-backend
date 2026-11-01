<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
  /**
   * Bilingual "About Us" page consumed by the Vue SPA and the Flutter app.
   *
   * Sections are typed blocks: the first text block is the story, then stats,
   * contact details and social links — AboutResource groups them into the
   * matching JSON arrays. Idempotent: children are rebuilt on every run.
   *
   * @var array<int, array{type: string, heading_en: string, heading_ar: string, body_en?: string, body_ar?: string, value?: string}>
   */
  private const SECTIONS = [
    [
      'type' => 'text',
      'heading_en' => 'Our Story',
      'heading_ar' => 'قصتنا',
      'body_en' => 'We started in 2021 with a small rail of essentials and one idea: clothes that feel good, fit well, and last longer than a season. Today we ship curated pieces to customers across the region.',
      'body_ar' => 'بدأنا في عام 2021 بخط صغير من الأساسيات وفكرة واحدة: ملابس مريحة وبتناسب جيدًا وتدوم أكثر من موسم. اليوم نوصل قطعًا مختارة إلى عملائنا في المنطقة.',
    ],
    [
      'type' => 'text',
      'heading_en' => 'Our Mission',
      'heading_ar' => 'مهمتنا',
      'body_en' => 'Make everyday dressing simple. We keep the catalog tight, the fabrics soft, and the prices honest — so finding something you love takes minutes, not hours.',
      'body_ar' => 'نجعل اختيار ملابسك اليومية أمرًا بسيطًا. نحافظ على تشكيلة محدودة والأقمشة ناعمة والأسعار صادقة — لتجد ما تحبه في دقائق لا ساعات.',
    ],
    [
      'type' => 'text',
      'heading_en' => 'Why Shop With Us',
      'heading_ar' => 'لماذا تتسوق معنا',
      'body_en' => 'Free delivery over a minimum order, easy 14-day returns, and a support team that answers in Arabic and English. Every piece is checked before it leaves our studio.',
      'body_ar' => 'توصيل مجاني فوق حد أدنى للطلب، وإرجاع سهل خلال 14 يومًا، وفريق دعم يجيب بالعربية والإنجليزية. يتم فحص كل قطعة قبل مغادرتها الاستوديو.',
    ],
    [
      'type' => 'stats',
      'heading_en' => 'Happy customers',
      'heading_ar' => 'عميل سعيد',
      'value' => '12k+',
    ],
    [
      'type' => 'stats',
      'heading_en' => 'Curated products',
      'heading_ar' => 'منتج مختار',
      'value' => '500+',
    ],
    [
      'type' => 'stats',
      'heading_en' => 'Years of experience',
      'heading_ar' => 'سنوات من الخبرة',
      'value' => '5',
    ],
    [
      'type' => 'contact',
      'heading_en' => 'Email',
      'heading_ar' => 'البريد الإلكتروني',
      'value' => 'hello@clothes-shop.test',
    ],
    [
      'type' => 'contact',
      'heading_en' => 'Phone',
      'heading_ar' => 'الهاتف',
      'value' => '+20 100 000 0000',
    ],
    [
      'type' => 'contact',
      'heading_en' => 'Address',
      'heading_ar' => 'العنوان',
      'value' => '12 Nile Street, Cairo, Egypt',
    ],
    [
      'type' => 'social',
      'heading_en' => 'Instagram',
      'heading_ar' => 'إنستغرام',
      'value' => 'https://instagram.com/clothes-shop',
    ],
    [
      'type' => 'social',
      'heading_en' => 'Facebook',
      'heading_ar' => 'فيسبوك',
      'value' => 'https://facebook.com/clothes-shop',
    ],
    [
      'type' => 'social',
      'heading_en' => 'TikTok',
      'heading_ar' => 'تيك توك',
      'value' => 'https://tiktok.com/@clothes-shop',
    ],
  ];

  /**
   * Seed the about page, its content blocks and two gallery images.
   */
  public function run(): void
  {
    $page = Page::updateOrCreate(
      ['slug' => 'about'],
      [
        'title_en' => 'About Us',
        'title_ar' => 'من نحن',
        'intro_en' => 'A bilingual clothing store built around timeless essentials, honest prices and pieces you will actually wear.',
        'intro_ar' => 'متجر ملابس ثنائي اللغة يقدّم أساسيات خالدة وأسعارًا صادقة وقطعًا سترتديها فعلاً.',
        'is_published' => true,
        'sort_order' => 1,
      ]
    );

    // Rebuild children so re-seeding never duplicates blocks or images.
    $page->sections()->delete();
    $page->images()->delete();

    foreach (self::SECTIONS as $index => $section) {
      $page->sections()->create($section + ['sort_order' => $index + 1]);
    }

    foreach ([0, 1] as $position) {
      $page->images()->create([
        'image_url' => ImageSeeder::PHOTOS[$position],
        'sort_order' => $position + 1,
      ]);
    }
  }
}
