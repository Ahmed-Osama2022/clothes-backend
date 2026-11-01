<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Exposes the public shape of a polymorphic image; the morph owner columns
 * (`imageable_type`/`imageable_id`) stay internal. Follows the same camelCase
 * keys and ISO-8601 timestamps as the other resources, so an image embedded in
 * a category, product or variant looks identical everywhere.
 */
class ImageResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      // DB column is image_url; the public JSON key stays "url".
      'url' => $this->image_url,
      'sortOrder' => $this->sort_order,
      'createdAt' => $this->created_at?->toIso8601String(),
      'updatedAt' => $this->updated_at?->toIso8601String(),
    ];
  }
}
