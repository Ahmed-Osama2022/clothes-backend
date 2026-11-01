<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
  /**
   * Curated Unsplash clothing/fashion photos (every link was checked and
   * returns a real image). Two per category and three per product are handed
   * out in order, so seeding is deterministic instead of random.
   *
   * Also used by ImageFactory, so factory-made images are never placeholders.
   *
   * @var array<int, string>
   */
  public const PHOTOS = [
    'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1445205170230-053b83016050?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1503341504253-dff4815485f1?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1542272604-787c3835535d?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1576566588028-4147f3842f27?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1527719327859-c6ce80353573?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1562157873-818bc0726f68?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1475178626620-a4d074967452?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1495121605193-b116b5b9c5fe?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1485462537746-965f33f7f6a7?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1509319117193-57bab727e09d?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1516762689617-e1cffcef479d?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1467043237213-65f2da53396f?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1487222477894-8943e31ef7b2?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1485968579580-b6d095142e6e?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1544441893-675973e31985?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1560243563-062bfc001d68?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1566206091558-7f218b696731?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1551232864-3f0890e580d9?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1567113463300-102a7eb3cb26?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1490114538077-0a7f8cb49891?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1525507119028-ed4c629a60a3?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1552374196-c4e7ffc6e126?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1571945153237-4929e783af4a?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1564859228273-274232fdb516?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1554568218-0f1715e72254?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1581044777550-4cfa60707c03?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1620012253295-c15cc3e65df4?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1594633313593-bab3825d0caf?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1548126032-079a0fb0099d?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1503944583220-79d8926ad5e2?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1544022613-e87ca75a784a?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1520975954732-35dd22299614?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1496217590455-aa63a8350eea?auto=format&fit=crop&w=900&h=1200&q=80',
    'https://images.unsplash.com/photo-1534030347209-467a5b0ad3e6?auto=format&fit=crop&w=900&h=1200&q=80',
  ];

  private const CATEGORY_IMAGES = 2;

  private const PRODUCT_IMAGES = 3;

  /**
   * Attach curated Unsplash photos to every category and product.
   *
   * Photos are handed out sequentially through PHOTOS (60 links for
   * 3×2 + 18×3 = 60 slots), so every gallery is distinct and stable across
   * re-seeds. saveMany()/create() fills imageable_type/imageable_id.
   */
  public function run(): void
  {
    $cursor = 0;

    $nextUrl = function () use (&$cursor): string {
      $url = self::PHOTOS[$cursor % count(self::PHOTOS)];
      $cursor++;

      return $url;
    };

    Category::orderBy('id')->get()->each(function (Category $category) use ($nextUrl) {
      $this->attachImages($category, self::CATEGORY_IMAGES, $nextUrl);
    });

    Product::orderBy('id')->get()->each(function (Product $product) use ($nextUrl) {
      $this->attachImages($product, self::PRODUCT_IMAGES, $nextUrl);
    });
  }

  /**
   * Create $count images for a polymorphic owner, numbered from 1 so the first
   * row is a stable hero image (the images() relations order by sort_order).
   *
   * @param  callable(): string  $nextUrl
   */
  private function attachImages(Model $owner, int $count, callable $nextUrl): void
  {
    for ($index = 1; $index <= $count; $index++) {
      $owner->images()->create([
        'image_url' => $nextUrl(),
        'sort_order' => $index,
      ]);
    }
  }
}
