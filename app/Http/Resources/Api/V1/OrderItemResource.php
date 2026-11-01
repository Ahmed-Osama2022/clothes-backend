<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Shapes a single order line.
 *
 * `unitPrice` is the price snapshot taken at checkout (the variant price, or
 * the parent product's price when the variant price is null) and never changes
 * afterwards. `lineTotal` is quantity × unitPrice, formatted as a decimal
 * string because the API never emits floats for money.
 */
class OrderItemResource extends JsonResource
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
      'productId' => $this->product_id,
      'productVariantId' => $this->product_variant_id,
      'quantity' => $this->quantity,
      'unitPrice' => $this->unit_price,
      'lineTotal' => number_format((float) $this->unit_price * $this->quantity, 2, '.', ''),
      // Compact nested snapshots: enough to render an order row without
      // pulling the full ProductResource (images, variants) per line.
      'product' => $this->whenLoaded('product', fn ($product) => $product ? [
        'id' => $product->id,
        'slug' => $product->slug,
        'name' => $product->name,
        'title' => $product->{'title_' . $locale} ?: $product->title_en,
      ] : null),
      'variant' => $this->whenLoaded('variant', fn ($variant) => $variant ? [
        'id' => $variant->id,
        'size' => $variant->size,
        'color' => $variant->{'color_' . $locale} ?: $variant->color_en,
        'colorHex' => $variant->color_hex,
        'sku' => $variant->sku,
      ] : null),
    ];
  }

  /**
   * Pick the requested locale when supported, otherwise the app default.
   */
  private function resolveLocale(Request $request): string
  {
    $locale = $request->query('lang', app()->getLocale());

    return in_array($locale, ['en', 'ar'], true) ? $locale : config('app.locale', 'en');
  }
}
