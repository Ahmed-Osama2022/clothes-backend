<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Gives a model an auto-generated unique `slug` and makes API route model
 * binding resolve by that slug instead of the numeric id.
 */
trait HasSlug
{
  /**
   * Eloquent auto-calls boot{TraitName}. Fill the slug on first save when the
   * caller did not supply one, so records are always addressable by slug.
   */
  protected static function bootHasSlug(): void
  {
    static::creating(function ($model) {
      if (blank($model->slug)) {
        $model->slug = $model->generateUniqueSlug($model->name);
      }
    });
  }

  /**
   * Tell route model binding (e.g. /api/products/{product}) to look the model
   * up by `slug` rather than the primary key.
   */
  public function getRouteKeyName(): string
  {
    return 'slug';
  }

  /**
   * Slugify the value and append -2, -3, ... until it no longer collides with
   * an existing row. Falls back to a random suffix when the value has no
   * ASCII-able characters (e.g. an Arabic-only name).
   */
  protected function generateUniqueSlug(string $value): string
  {
    $base = Str::slug($value) ?: Str::random(8);
    $slug = $base;
    $suffix = 2;

    while (static::query()->where('slug', $slug)->exists()) {
      $slug = "{$base}-{$suffix}";
      $suffix++;
    }

    return $slug;
  }
}
