<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');


// Test route
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
  return response()->json($orders);
});

// Get all categories with their items
Route::get('/categories', function () {
  $categories = \App\Models\Category::with('products')->get();
  return response()->json($categories);
});

// GEt all products with their sizes
Route::get('/products', function () {
  $products = \App\Models\Product::with('sizes')->get();
  return response()->json($products);
});


// Get all products with their sizes and images
Route::get('/products/images', function () {
  $products = \App\Models\Product::with(['sizes', 'images'])->get();
  return response()->json($products);
});
