<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
  /**
   * List categories in editorial order, with their gallery and product count.
   */
  public function index()
  {
    $categories = Category::query()
      ->with('images')
      ->withCount('products')
      ->orderBy('sort_order')
      ->orderBy('id')
      ->get();

    return CategoryResource::collection($categories);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Show a single category with its images and products.
   * Returns a 404 payload when the slug matches no category.
   */
  public function show(string $category)
  {
    $item = Category::withCount('products')
      ->with(['images', 'products'])
      ->where('slug', $category)
      ->first();

    if (! $item) {
      return response()->json([
        'message' => "Category ($category) not found, try to refresh the category list or use the correct slug name.",
        'status' => 404,
      ], 404);
    }

    return new CategoryResource($item);
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
