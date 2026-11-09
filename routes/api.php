<?php

use App\Http\Controllers\Api\V1\AboutController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Legacy current-user endpoint kept for existing clients; the documented
// versioned alternative is GET /api/v1/auth/me (returns roles, camelCase).
Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');


// API_v1 Routes
Route::group(['prefix' => 'v1', 'namespace' => "App\Http\Controllers\Api\V1"], function () {
  // Authentication
  // Stateless Sanctum bearer tokens: register/login hand one out, logout
  // revokes it, me identifies the caller. Login and register are throttled
  // per IP to slow credential stuffing and fake sign-ups.
  Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('register', 'register')->middleware('throttle:6,1');
    Route::post('login', 'login')->middleware('throttle:6,1');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
    Route::get('me', 'me')->middleware('auth:sanctum');
  });

  // Categories
  // Route::apiResource('categories', 'CategoryController'); // OR
  Route::apiResource('categories', CategoryController::class);

  // Products
  Route::apiResource('products', 'ProductController');

  // Orders
  // Every order belongs to a customer, so checkout requires a Sanctum-
  // authenticated caller (the storefront posts with the customer's token).
  Route::apiResource('orders', 'OrderController')->middleware('auth:sanctum');

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
