<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
  /** @use HasFactory<\Database\Factories\OrderItemFactory> */
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'order_id',
    'product_id',
    'product_variant_id',
    'quantity',
    'unit_price',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'quantity' => 'integer',
      'unit_price' => 'decimal:2',
    ];
  }

  /**
   * The order this line belongs to.
   */
  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class);
  }

  /**
   * The product purchased on this line (kept for quick filtering/history).
   */
  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }

  /**
   * The exact size/color variant that was bought.
   */
  public function variant(): BelongsTo
  {
    return $this->belongsTo(ProductVariant::class, 'product_variant_id');
  }
}
