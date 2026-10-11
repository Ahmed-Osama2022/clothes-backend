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


Route::get('/orders', function () {
  $orders  = \App\Models\Order::with('items')->get();
  return response()->json($orders);
});
