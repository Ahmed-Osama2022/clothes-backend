<?php

use App\Http\Controllers\Api\V1\AboutController;
use App\Http\Controllers\Api\V1\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');


// API_v1 Routes
Route::group(['prefix' => 'v1', 'namespace' => "App\Http\Controllers\Api\V1"], function () {
  // Categories
  // Route::apiResource('categories', 'CategoryController'); // OR
  Route::apiResource('categories', CategoryController::class);

  // Products
  Route::apiResource('products', 'ProductController');

  // Orders
  // Checkout (POST) is public so guests can buy; reading, status changes and
  // deletion require a Sanctum-authenticated caller (owner or admin).
  Route::apiResource('orders', 'OrderController')
    ->middlewareFor(['index', 'show', 'update', 'destroy'], 'auth:sanctum');

  // Static content pages (aggregated for the Vue SPA and the Flutter app).
  Route::get('about', [AboutController::class, 'show'])->name('api.v1.about');
});


/**
 * |----------------------------------------------------------------------|
 * | TEST: API test routes
 * |----------------------------------------------------------------------|
 */
// TEST: route
Route::get('/test', function () {
  return ['message' => 'Test route is working!'];
});
