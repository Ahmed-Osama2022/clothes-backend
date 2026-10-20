<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Exposes only the public shape of a polymorphic image; the morph owner columns
 * (`imageable_type`/`imageable_id`) stay internal.
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
      'url' => $this->url,
      'sort_order' => $this->sort_order,
    ];
  }
}
