<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public shape of a user. `password`, `remember_token` and the Sanctum token
 * rows never leave the model, and `roles` is emitted only when the controller
 * has eager-loaded it (a bare auth response needs no extra query).
 */
class UserResource extends JsonResource
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
      'name' => $this->name,
      'email' => $this->email,
      // Machine names ("admin", "customer"), not pivot rows.
      'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')->values()->all()),
      'emailVerifiedAt' => $this->email_verified_at?->toIso8601String(),
      'createdAt' => $this->created_at?->toIso8601String(),
      'updatedAt' => $this->updated_at?->toIso8601String(),
    ];
  }
}
