<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
  /** @use HasFactory<\Database\Factories\ProductFactory> */
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'category_id',
    'slug',
    'name',
    'title_en',
    'title_ar',
    'description_en',
    'description_ar',
    'price',
    'is_featured',
    'sort_order'
  ];


  /**
   * The category this product belongs to.
   */
  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }

  public function images(): MorphMany
  {
    return $this->morphMany(Image::class, 'imageable')->orderBy('sort_order');
  }
}
