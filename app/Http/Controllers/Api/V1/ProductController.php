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
   */
  public function show(Product $product)
  {
    return new ProductResource(
      $product->load(['category', 'images', 'variants'])
    );
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
