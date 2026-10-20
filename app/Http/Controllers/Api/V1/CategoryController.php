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
   */
  public function show(Category $category)
  {
    return new CategoryResource(
      $category->loadCount('products')->load(['images', 'products'])
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
