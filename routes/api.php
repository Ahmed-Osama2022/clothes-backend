<?php

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
  Route::apiResource('orders', 'OrderController');
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
