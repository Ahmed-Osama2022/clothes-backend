<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\OrderConflictException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
  /**
   * Relations every order payload needs. Kept in one place so index, store,
   * show and update always return the same shape.
   */
  private const RELATIONS = ['user', 'items.product', 'items.variant'];

  /**
   * Admin order list, newest first, with the customer and their lines.
   */
  public function index()
  {
    Gate::authorize('viewAny', Order::class);

    $orders = Order::query()
      ->with(self::RELATIONS)
      ->withCount('items')
      ->latest('id')
      ->get();

    return OrderResource::collection($orders);
  }

  /**
   * Place an order (guest or authenticated).
   *
   * The whole checkout runs in one transaction:
   *  1. read the chosen variants,
   *  2. reject inactive ones (409),
   *  3. decrement stock with a guarded UPDATE (quantity >= ordered), which is
   *     the actual oversell gate — if it affects no row, another request took
   *     the last unit and we abort with 409,
   *  4. write the order and its price snapshots, then the total.
   *
   * Prices always come from the database, never from the request.
   */
  public function store(StoreOrderRequest $request)
  {
    // Guests have no account; a bearer token or session links the order.
    $user = $request->user('sanctum') ?? $request->user();

    $lines = $request->validated('items');

    $order = DB::transaction(function () use ($lines, $user) {
      $variants = ProductVariant::query()
        ->with('product')
        ->whereIn('id', array_column($lines, 'product_variant_id'))
        ->get()
        ->keyBy('id');

      $items = [];
      $totalCents = 0;

      foreach ($lines as $line) {
        /** @var ProductVariant $variant */
        $variant = $variants->get($line['product_variant_id']);
        $quantity = (int) $line['quantity'];

        if (! $variant->is_active) {
          throw OrderConflictException::unavailable($variant);
        }

        // Guarded decrement: the WHERE clause makes the check and the write a
        // single atomic statement, so two simultaneous checkouts cannot both
        // pass the stock check.
        $decremented = ProductVariant::query()
          ->whereKey($variant->id)
          ->where('is_active', true)
          ->where('quantity', '>=', $quantity)
          ->decrement('quantity', $quantity);

        if ($decremented === 0) {
          throw OrderConflictException::insufficientStock($variant, $quantity);
        }

        // Variant price wins; a null variant price falls back to the product.
        $unitPrice = $variant->price ?? $variant->product->price;

        $items[] = [
          'product_id' => $variant->product_id,
          'product_variant_id' => $variant->id,
          'quantity' => $quantity,
          'unit_price' => $unitPrice,
        ];

        // Sum in cents so decimal money never picks up float rounding drift.
        $totalCents += (int) round((float) $unitPrice * 100) * $quantity;
      }

      $order = Order::create([
        'user_id' => $user?->id,
        'status' => 'pending',
        'total_amount' => number_format($totalCents / 100, 2, '.', ''),
      ]);

      $order->items()->createMany($items);

      return $order;
    });

    $order->load(self::RELATIONS)->loadCount('items');

    return (new OrderResource($order))->response()->setStatusCode(201);
  }

  /**
   * Show one order to its owner or an admin.
   * Explicit lookup keeps the project's resource-named JSON 404 body.
   */
  public function show(Request $request, string $order)
  {
    $item = $this->findOrder($order);

    if (! $item) {
      return $this->notFound($order);
    }

    Gate::authorize('view', $item);

    return new OrderResource($item);
  }

  /**
   * Update the order status (admin only). Items, price snapshots and the total
   * are historical records and stay immutable.
   */
  public function update(UpdateOrderRequest $request, string $order)
  {
    $item = $this->findOrder($order);

    if (! $item) {
      return $this->notFound($order);
    }

    Gate::authorize('update', $item);

    $item->update(['status' => $request->validated('status')]);

    $item->load(self::RELATIONS)->loadCount('items');

    return new OrderResource($item);
  }

  /**
   * Delete an order (admin only). `order_items` rows cascade at the DB level.
   */
  public function destroy(string $order)
  {
    $item = $this->findOrder($order);

    if (! $item) {
      return $this->notFound($order);
    }

    Gate::authorize('delete', $item);

    $item->delete();

    return response()->noContent();
  }

  /**
   * Look an order up by id with everything the resource renders.
   */
  private function findOrder(string $id): ?Order
  {
    return Order::query()
      ->with(self::RELATIONS)
      ->withCount('items')
      ->find($id);
  }

  /**
   * Shared not-found payload (see AGENTS.md § 5).
   */
  private function notFound(string $id)
  {
    return response()->json([
      'message' => "Order ($id) not found, try to refresh the order list or use the correct id.",
      'status' => 404,
    ], 404);
  }
}
