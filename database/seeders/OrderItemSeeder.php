<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
  /**
   * Give every order 1-2 line items, snapshotting the product's current price,
   * then recompute total_amount so the stored total matches the seeded lines.
   */
  public function run(): void
  {
    $sizes = ['XS', 'S', 'M', 'L', 'XL'];

    Order::all()->each(function (Order $order) use ($sizes) {
      Product::inRandomOrder()
        ->take(fake()->numberBetween(1, 2))
        ->get()
        ->each(function (Product $product) use ($order, $sizes) {
          $order->items()->create([
            'product_id' => $product->id,
            'size' => fake()->randomElement($sizes),
            'quantity' => fake()->numberBetween(1, 3),
            'unit_price' => $product->price,
          ]);
        });

      $total = $order->items()->get()
        ->sum(fn ($item) => $item->quantity * $item->unit_price);

      $order->update(['total_amount' => $total]);
    });
  }
}
