<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends Model
{
  /** @use HasFactory<\Database\Factories\CategoryFactory> */
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'slug',
    'name',
    'title_en',
    'title_ar',
    'description_en',
    'description_ar',
  ];

  /**
   * All images attached to this category, ordered so the first row can act
   * as the tile/hero image while the rest form a gallery.
   */
  public function images(): MorphMany
  {
    return $this->morphMany(Image::class, 'imageable')->orderBy('sort_order');
  }
}
