<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public "About Us" payload for the Vue SPA and the Flutter app.
 *
 * Content is returned already translated and structured — blocks are grouped by
 * type so each client can render them natively (no HTML parsing):
 *   text    → heading + body
 *   stats   → label + value
 *   contact → label + value
 *   social  → platform + url
 *
 * Localized text comes from the `{field}_{locale}` columns with an `_en`
 * fallback, using the same `?lang=` query value as the catalog resources.
 */
class AboutResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $locale = $this->resolveLocale($request);
    $sections = $this->sections->groupBy('type');

    // Sections are a different model, so they resolve their own columns.
    $localized = fn($section, string $field): ?string => $section->{$field . '_' . $locale}
      ?: $section->{$field . '_en'};

    return [
      'slug' => $this->slug,
      'title' => $this->translated('title', $locale) ?? $this->slug,
      'intro' => $this->translated('intro', $locale),
      'sections' => $sections->get('text', collect())->map(fn($section) => [
        'heading' => $localized($section, 'heading'),
        'body' => $localized($section, 'body'),
      ])->values(),
      'stats' => $sections->get('stats', collect())->map(fn($section) => [
        'label' => $localized($section, 'heading'),
        'value' => $section->value,
      ])->values(),
      'contact' => $sections->get('contact', collect())->map(fn($section) => [
        'label' => $localized($section, 'heading'),
        'value' => $section->value,
      ])->values(),
      'social' => $sections->get('social', collect())->map(fn($section) => [
        'platform' => $localized($section, 'heading'),
        'url' => $section->value,
      ])->values(),
      'images' => ImageResource::collection($this->whenLoaded('images')),
      'updatedAt' => $this->updated_at?->toIso8601String(),
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

  /**
   * Read a localized column, falling back to English when the locale is empty.
   */
  private function translated(string $field, string $locale): ?string
  {
    return $this->{$field . '_' . $locale} ?: $this->{$field . '_en'};
  }
}
