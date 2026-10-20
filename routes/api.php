<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');


// TEST: route
Route::get('/test', function () {
  return response()->json(['message' => 'Test route is working!']);
});



/**
 * |----------------------------------------------------------------------|
 * | TEST: API test routes
 * |----------------------------------------------------------------------|
 */
// Get all orders with their items
Route::get('/orders', function () {
  $orders  = \App\Models\Order::with('items')->get();

  return response()->json([
    'Count' => $orders->count() . ' Orders.',
    'orders' => $orders,
  ]);
});

// Get the order with its items by order ID
Route::get('/orders/{order}', function (\App\Models\Order $order) {
  $order->load('items');
  return response()->json($order);
});

// Get all ordered items with their product_variants
Route::get('/orders/{order}/variant', function (\App\Models\Order $order) {
  $items = $order->items()->with('variant')->get();
  return response()->json($items);
});

// Get all categories with their items
Route::get('/categories', function () {
  $categories = \App\Models\Category::with('products')->get();
  return response()->json($categories);
});

// Get all categories with their items
Route::get('/categories/{category}', function (\App\Models\Category $category) {
  $category->load('products');
  return response()->json($category);
});

// GEt all products with their variants
Route::get('/products', function () {
  $products = \App\Models\Product::with('variants')->get();
  return response()->json($products);
});


// Get all products with their variants and images
Route::get('/products/images', function () {
  $products = \App\Models\Product::with(['variants', 'images'])->get();
  return response()->json($products);
});


// Get all users
Route::get('/users', function () {
  $users = \App\Models\User::with('roles')->get();
  return response()->json([
    'Count' => $users->count() . ' Users.',
    'users' => $users,

  ]);
});
