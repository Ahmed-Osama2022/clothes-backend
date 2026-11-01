<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
  /** @use HasFactory<\Database\Factories\OrderFactory> */
  use HasFactory;

  /**
   * The full lifecycle, mirroring the `status` enum in the migration so the
   * form request, resource and docs share one source of truth.
   */
  public const STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'status',
    'total_amount',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'total_amount' => 'decimal:2',
    ];
  }

  /**
   * The customer who placed the order (null for guest checkouts).
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * The line items that make up this order, in the order they were added.
   */
  public function items(): HasMany
  {
    return $this->hasMany(OrderItem::class)->orderBy('id');
  }
}
