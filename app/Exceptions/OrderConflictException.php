<?php

namespace App\Exceptions;

use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Raised inside the order transaction when a line can no longer be fulfilled.
 *
 * Extending RuntimeException lets DB::transaction() roll the whole checkout
 * back before the handler rethrows it, while render() returns the project's
 * standard error body ({ message, status }) with a 409.
 */
class OrderConflictException extends RuntimeException
{
  /**
   * Render the exception as the API's JSON error shape.
   */
  public function render(Request $request): JsonResponse
  {
    return response()->json([
      'message' => $this->getMessage(),
      'status' => 409,
    ], 409);
  }

  /**
   * The ordered quantity is larger than what is left in stock.
   */
  public static function insufficientStock(ProductVariant $variant, int $requested): self
  {
    return new self(sprintf(
      'Size «%s» is out of stock: only %d left, %d requested.',
      $variant->size,
      $variant->quantity,
      $requested
    ));
  }

  /**
   * The variant was deactivated, so it cannot be sold anymore.
   */
  public static function unavailable(ProductVariant $variant): self
  {
    return new self(sprintf('Size «%s» is no longer available.', $variant->size));
  }
}
