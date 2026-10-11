<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
  /** @use HasFactory<\Database\Factories\RoleFactory> */
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
  ];

  /**
   * The users that hold this role.
   */
  public function users(): BelongsToMany
  {
    return $this->belongsToMany(User::class);
  }
}
