<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Shapes a product for the storefront. Localized text is resolved from the
 * `{field}_{locale}` columns using the `?locale=` query value, falling back to
 * the app locale and then `_en`. Variants and images are only emitted when the
 * controller has eager-loaded them.
 */
class ProductResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $locale = $this->resolveLocale($request);

    return [
      'id' => $this->id,
      'slug' => $this->slug,
      'name' => $this->name,
      'title' => $this->translated('title', $locale) ?? $this->name,
      'description' => $this->translated('description', $locale),
      'price' => $this->price,
      'isFeatured' => (bool) $this->is_featured,
      'sortOrder' => $this->sort_order,
      'category' => $this->whenLoaded('category', fn() => new CategoryResource($this->category)),
      'images' => ImageResource::collection($this->whenLoaded('images')),
      'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
      'createdAt' => $this->created_at?->toIso8601String(),
      'updatedAt' => $this->updated_at?->toIso8601String(),
    ];
  }

  /**
   * Pick the requested locale when supported, otherwise the app default.
   */
  private function resolveLocale(Request $request): string
  {
    $locale = $request->query('lang', app()->getLocale());

    return in_array($locale, ['en', 'ar'], true) ? $locale : 'en';
  }

  /**
   * Read a localized column, falling back to English when the locale is empty.
   */
  private function translated(string $field, string $locale): ?string
  {
    return $this->{$field . '_' . $locale} ?: $this->{$field . '_en'};
  }
}
