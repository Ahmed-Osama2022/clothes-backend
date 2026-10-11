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
    'size',
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
   * The product purchased on this line. The chosen `size` is stored on the
   * item (not read from the product) so the line is a historical snapshot.
   */
  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }
}
