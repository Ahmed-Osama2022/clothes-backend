<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
  /**
   * The shop-wide order list is a back-office view (customers will use their
   * own history endpoint once it exists).
   */
  public function viewAny(User $user): bool
  {
    return $user->isAdmin();
  }

  /**
   * A customer sees only their own orders; admins see every order.
   */
  public function view(User $user, Order $order): bool
  {
    return $user->isAdmin() || $order->user_id === $user->id;
  }

  /**
   * Checkout is authenticated: the route middleware guarantees a customer.
   */
  public function create(User $user): bool
  {
    return true;
  }

  /**
   * Status changes are the fulfilment team's job.
   */
  public function update(User $user, Order $order): bool
  {
    return $user->isAdmin();
  }

  /**
   * Deleting an order erases its history, so admins only.
   */
  public function delete(User $user, Order $order): bool
  {
    return $user->isAdmin();
  }
}
