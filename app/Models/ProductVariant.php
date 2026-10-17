<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
  /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'product_id',
    'size',
    'color_en',
    'color_ar',
    'color_hex',
    'sku',
    'price',
    'quantity',
    'is_active',
    'sort_order',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'price' => 'decimal:2',
      'quantity' => 'integer',
      'is_active' => 'boolean',
    ];
  }

  /**
   * Assign a unique SKU before the first save when none was supplied.
   */
  protected static function booted(): void
  {
    static::creating(function (self $variant) {
      if (blank($variant->sku)) {
        $variant->sku = $variant->generateUniqueSku();
      }
    });
  }

  /**
   * The product this variant belongs to.
   */
  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }

  /**
   * Variant-specific images (polymorphic, shared with categories/products).
   */
  public function images(): MorphMany
  {
    return $this->morphMany(Image::class, 'imageable')->orderBy('sort_order');
  }

  /**
   * Build a SKU from the product slug + size + color, e.g.
   * "classic-white-tee-M-WHITE". Appends -2, -3, ... on collisions.
   */
  protected function generateUniqueSku(): string
  {
    $parts = [$this->product?->slug, $this->size, $this->color_en];
    $base = strtoupper(Str::slug(implode(' ', array_filter($parts)), '-'));
    $base = $base ?: strtoupper(Str::random(8));

    $sku = $base;
    $suffix = 2;

    while (static::query()->where('sku', $sku)->exists()) {
      $sku = "{$base}-{$suffix}";
      $suffix++;
    }

    return $sku;
  }
}
