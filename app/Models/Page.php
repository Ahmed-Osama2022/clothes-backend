<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Page extends Model
{
  /** @use HasFactory<\Database\Factories\PageFactory> */
  use HasFactory, HasSlug;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'slug',
    'title_en',
    'title_ar',
    'intro_en',
    'intro_ar',
    'is_published',
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
      'is_published' => 'boolean',
    ];
  }

  /**
   * The ordered content blocks that build the page (text, stats, contact, social).
   */
  public function sections(): HasMany
  {
    return $this->hasMany(PageSection::class)->orderBy('sort_order');
  }

  /**
   * Page hero/gallery images (same polymorphic table as categories/products).
   */
  public function images(): MorphMany
  {
    return $this->morphMany(Image::class, 'imageable')->orderBy('sort_order');
  }
}
