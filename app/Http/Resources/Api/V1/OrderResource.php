<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Shapes an order for the API.
 *
 * `totalAmount` is the server-computed sum of the line snapshots — it is never
 * read from the request. `customer` is null for guest checkouts, and both
 * `customer` and `items` are only emitted when the controller has eager-loaded
 * them (so list endpoints can stay light).
 */
class OrderResource extends JsonResource
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
      'status' => $this->status,
      'totalAmount' => $this->total_amount,
      'itemsCount' => $this->whenCounted('items'),
      // Guest order => no account to expose.
      'customer' => $this->whenLoaded('user', fn ($user) => $user ? [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
      ] : null),
      'items' => OrderItemResource::collection($this->whenLoaded('items')),
      'createdAt' => $this->created_at?->toIso8601String(),
      'updatedAt' => $this->updated_at?->toIso8601String(),
    ];
  }
}
