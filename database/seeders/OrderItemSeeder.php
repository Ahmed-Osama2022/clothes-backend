<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
  /**
   * Give every order 1-2 line items, each pointing at a random variant of the
   * product and snapshotting its effective price, then recompute total_amount
   * so the stored total matches the seeded lines.
   *
   * Idempotent: orders that already have lines are left untouched, so
   * re-running `db:seed` never adds duplicate lines (or double-charges a total).
   */
  public function run(): void
  {
    Order::all()->each(function (Order $order) {
      if ($order->items()->exists()) {
        return;
      }

      Product::inRandomOrder()
        ->take(fake()->numberBetween(1, 2))
        ->get()
        ->each(function (Product $product) use ($order) {
          $variant = $product->variants()->inRandomOrder()->first();

          if (! $variant) {
            return;
          }

          $order->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            // Variant price falls back to the product's price when null.
            'unit_price' => $variant->price ?? $product->price,
            'quantity' => fake()->numberBetween(1, 3),
          ]);
        });

      $total = $order->items()->get()
        ->sum(fn ($item) => $item->quantity * $item->unit_price);

      // Keep the seeded "placed at" timestamp intact: the total is corrected
      // to match the lines, but that is not a real update to the order.
      $order->timestamps = false;
      $order->update(['total_amount' => $total]);
      $order->timestamps = true;
    });
  }
}
