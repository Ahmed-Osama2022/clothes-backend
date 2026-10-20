<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
  /**
   * List products in editorial order, with their gallery and variants.
   */
  public function index()
  {
    $products = Product::query()
      ->with(['images', 'variants'])
      ->orderBy('sort_order')
      ->orderBy('id')
      ->get();

    return ProductResource::collection($products);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Show a single product with its category, images and variants.
   * Returns a 404 payload when the slug matches no product.
   */
  public function show(string $product)
  {
    $item = Product::with(['category', 'images', 'variants'])
      ->where('slug', $product)
      ->first();

    if (! $item) {
      return response()->json([
        'message' => "Product ($product) not found, try to refresh the product list or use the correct slug name.",
        'status' => 404,
      ], 404);
    }

    return new ProductResource($item);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
