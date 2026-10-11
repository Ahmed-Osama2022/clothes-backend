<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
  /** @use HasFactory<\Database\Factories\ImageFactory> */
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'image_url',
    'sort_order',
  ];

  /**
   * The owning record this image is attached to (Category, Product, …).
   * Resolved polymorphically through the `imageable_type`/`imageable_id` pair.
   */
  public function imageable(): MorphTo
  {
    return $this->morphTo();
  }
}
