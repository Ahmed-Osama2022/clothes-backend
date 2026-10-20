<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Shapes a single size/color variant. `price` is the variant override and may
 * be null, in which case clients use the parent product's price. The localized
 * `color` is resolved from `color_en`/`color_ar`.
 */
class ProductVariantResource extends JsonResource
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
      'size' => $this->size,
      'color' => $this->translated('color', $locale),
      'colorHex' => $this->color_hex,
      'sku' => $this->sku,
      'price' => $this->price,
      'quantity' => $this->quantity,
      'isActive' => (bool) $this->is_active,
      'sortOrder' => $this->sort_order,
      'images' => ImageResource::collection($this->whenLoaded('images')),
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
