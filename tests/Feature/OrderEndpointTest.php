<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderEndpointTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Create a user holding the given role (roles are seeded by name).
   */
  private function userWithRole(string $role): User
  {
    $user = User::factory()->create();
    $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

    return $user;
  }

  /**
   * Create a sellable variant. A null price falls back to the product price,
   * matching the catalog rules.
   */
  private function variant(array $attributes = []): ProductVariant
  {
    return ProductVariant::factory()->create($attributes);
  }

  public function test_checkout_requires_authentication(): void
  {
    $variant = $this->variant(['quantity' => 5]);

    $this->postJson('/api/v1/orders', [
      'items' => [['product_variant_id' => $variant->id, 'quantity' => 1]],
    ])->assertStatus(401);

    $this->assertDatabaseCount('orders', 0);
  }

  public function test_customer_can_place_an_order(): void
  {
    $customer = $this->userWithRole('customer');
    $variant = $this->variant(['size' => 'M', 'quantity' => 5, 'price' => '19.99']);
    $fallback = $this->variant([
      'size' => 'L',
      'quantity' => 3,
      'price' => null,
      'product_id' => Product::factory()->create(['price' => '49.50'])->id,
    ]);

    $response = $this->actingAs($customer, 'sanctum')->postJson('/api/v1/orders', [
      'items' => [
        ['product_variant_id' => $variant->id, 'quantity' => 2],
        ['product_variant_id' => $fallback->id, 'quantity' => 1],
      ],
    ]);

    $response->assertCreated()
      ->assertJsonPath('data.status', 'pending')
      // 19.99 × 2 + 49.50
      ->assertJsonPath('data.totalAmount', '89.48')
      ->assertJsonPath('data.customer.id', $customer->id)
      ->assertJsonPath('data.customer.email', $customer->email)
      ->assertJsonPath('data.itemsCount', 2)
      ->assertJsonPath('data.items.0.unitPrice', '19.99')
      ->assertJsonPath('data.items.0.lineTotal', '39.98')
      ->assertJsonPath('data.items.0.variant.size', 'M')
      ->assertJsonCount(2, 'data.items');

    $this->assertDatabaseHas('orders', [
      'user_id' => $customer->id,
      'status' => 'pending',
      'total_amount' => '89.48',
    ]);

    // Stock is decremented for every line.
    $this->assertSame(3, $variant->fresh()->quantity);
    $this->assertSame(2, $fallback->fresh()->quantity);
  }

  public function test_orders_require_a_user_at_the_database_level(): void
  {
    $this->expectException(QueryException::class);

    // The schema refuses orphan orders outright (user_id is NOT NULL).
    Order::factory()->create(['user_id' => null]);
  }

  public function test_order_is_rejected_when_stock_is_insufficient(): void
  {
    $customer = $this->userWithRole('customer');
    $variant = $this->variant(['size' => 'M', 'quantity' => 2, 'price' => '15.00']);

    $response = $this->actingAs($customer, 'sanctum')->postJson('/api/v1/orders', [
      'items' => [['product_variant_id' => $variant->id, 'quantity' => 3]],
    ]);

    $response->assertStatus(409)
      ->assertJsonPath('message', 'Size «M» is out of stock: only 2 left, 3 requested.')
      ->assertJsonPath('status', 409);

    // Nothing was written and the stock is untouched.
    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);
    $this->assertSame(2, $variant->fresh()->quantity);
  }

  public function test_order_is_rejected_when_a_variant_is_inactive(): void
  {
    $customer = $this->userWithRole('customer');
    $variant = $this->variant(['size' => 'XL', 'quantity' => 10, 'is_active' => false]);

    $this->actingAs($customer, 'sanctum')->postJson('/api/v1/orders', [
      'items' => [['product_variant_id' => $variant->id, 'quantity' => 1]],
    ])
      ->assertStatus(409)
      ->assertJsonPath('message', 'Size «XL» is no longer available.');

    $this->assertDatabaseCount('orders', 0);
  }

  public function test_order_requires_at_least_one_line(): void
  {
    $this->actingAs($this->userWithRole('customer'), 'sanctum')
      ->postJson('/api/v1/orders', ['items' => []])
      ->assertStatus(422)
      ->assertJsonValidationErrors('items');
  }

  public function test_order_rejects_unknown_variants(): void
  {
    $this->actingAs($this->userWithRole('customer'), 'sanctum')
      ->postJson('/api/v1/orders', [
        'items' => [['product_variant_id' => 999999, 'quantity' => 1]],
      ])
      ->assertStatus(422)
      ->assertJsonValidationErrors('items.0.product_variant_id');

    $this->assertDatabaseCount('orders', 0);
  }

  public function test_order_rejects_duplicate_variant_lines(): void
  {
    $variant = $this->variant(['quantity' => 10]);

    $this->actingAs($this->userWithRole('customer'), 'sanctum')
      ->postJson('/api/v1/orders', [
        'items' => [
          ['product_variant_id' => $variant->id, 'quantity' => 1],
          ['product_variant_id' => $variant->id, 'quantity' => 2],
        ],
      ])->assertStatus(422);

    $this->assertDatabaseCount('orders', 0);
  }

  public function test_order_rejects_non_positive_quantities(): void
  {
    $variant = $this->variant(['quantity' => 10]);

    $this->actingAs($this->userWithRole('customer'), 'sanctum')
      ->postJson('/api/v1/orders', [
        'items' => [['product_variant_id' => $variant->id, 'quantity' => 0]],
      ])
      ->assertStatus(422)
      ->assertJsonValidationErrors('items.0.quantity');
  }

  public function test_admin_can_list_every_order(): void
  {
    Order::factory()->count(3)->create();

    $this->actingAs($this->userWithRole('admin'), 'sanctum')
      ->getJson('/api/v1/orders')
      ->assertOk()
      ->assertJsonCount(3, 'data')
      ->assertJsonPath('data.0.itemsCount', 0)
      // Every listed order exposes its customer (no orphan orders).
      ->assertJsonPath('data.0.customer.id', fn ($id) => is_int($id));
  }

  public function test_customer_cannot_list_every_order(): void
  {
    $this->actingAs($this->userWithRole('customer'), 'sanctum')
      ->getJson('/api/v1/orders')
      ->assertStatus(403);
  }

  public function test_guest_cannot_list_orders(): void
  {
    $this->getJson('/api/v1/orders')->assertStatus(401);
  }

  public function test_owner_can_view_their_order(): void
  {
    $customer = $this->userWithRole('customer');
    $order = Order::factory()->create(['user_id' => $customer->id]);

    $this->actingAs($customer, 'sanctum')
      ->getJson("/api/v1/orders/{$order->id}")
      ->assertOk()
      ->assertJsonPath('data.id', $order->id)
      ->assertJsonPath('data.customer.id', $customer->id);
  }

  public function test_another_customer_cannot_view_someone_elses_order(): void
  {
    $owner = $this->userWithRole('customer');
    $other = $this->userWithRole('customer');
    $order = Order::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other, 'sanctum')
      ->getJson("/api/v1/orders/{$order->id}")
      ->assertStatus(403);
  }

  public function test_admin_can_view_any_customers_order(): void
  {
    $order = Order::factory()->create();

    $this->actingAs($this->userWithRole('admin'), 'sanctum')
      ->getJson("/api/v1/orders/{$order->id}")
      ->assertOk()
      ->assertJsonPath('data.id', $order->id)
      ->assertJsonPath('data.customer.id', $order->user_id);
  }

  public function test_unknown_order_returns_the_json_404_payload(): void
  {
    $this->actingAs($this->userWithRole('admin'), 'sanctum')
      ->getJson('/api/v1/orders/999999')
      ->assertStatus(404)
      ->assertExactJson([
        'message' => 'Order (999999) not found, try to refresh the order list or use the correct id.',
        'status' => 404,
      ]);
  }

  public function test_admin_can_update_the_order_status(): void
  {
    $order = Order::factory()->status('pending')->create();

    $this->actingAs($this->userWithRole('admin'), 'sanctum')
      ->patchJson("/api/v1/orders/{$order->id}", ['status' => 'shipped'])
      ->assertOk()
      ->assertJsonPath('data.status', 'shipped');

    $this->assertSame('shipped', $order->fresh()->status);
  }

  public function test_update_rejects_an_unknown_status(): void
  {
    $order = Order::factory()->create();

    $this->actingAs($this->userWithRole('admin'), 'sanctum')
      ->patchJson("/api/v1/orders/{$order->id}", ['status' => 'lost'])
      ->assertStatus(422)
      ->assertJsonValidationErrors('status');
  }

  public function test_customer_cannot_update_an_order(): void
  {
    $customer = $this->userWithRole('customer');
    $order = Order::factory()->create(['user_id' => $customer->id]);

    $this->actingAs($customer, 'sanctum')
      ->patchJson("/api/v1/orders/{$order->id}", ['status' => 'delivered'])
      ->assertStatus(403);
  }

  public function test_admin_can_delete_an_order(): void
  {
    $order = Order::factory()->create();

    $this->actingAs($this->userWithRole('admin'), 'sanctum')
      ->deleteJson("/api/v1/orders/{$order->id}")
      ->assertNoContent();

    $this->assertDatabaseMissing('orders', ['id' => $order->id]);
  }

  public function test_customer_cannot_delete_an_order(): void
  {
    $customer = $this->userWithRole('customer');
    $order = Order::factory()->create(['user_id' => $customer->id]);

    $this->actingAs($customer, 'sanctum')
      ->deleteJson("/api/v1/orders/{$order->id}")
      ->assertStatus(403);

    $this->assertDatabaseHas('orders', ['id' => $order->id]);
  }

  public function test_deleting_a_user_deletes_their_orders(): void
  {
    $customer = $this->userWithRole('customer');
    $order = Order::factory()->create(['user_id' => $customer->id]);

    $customer->delete();

    // CASCADE at the database level: no orphan orders can exist.
    $this->assertDatabaseMissing('orders', ['id' => $order->id]);
  }
}
