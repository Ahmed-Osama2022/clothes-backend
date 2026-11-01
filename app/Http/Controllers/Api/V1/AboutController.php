<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AboutResource;
use App\Models\Page;
use Illuminate\Http\Request;

class AboutController extends Controller
{
  /**
   * The public "about us" page for the storefront apps.
   *
   * Returns the translated content tree plus the gallery images. Because this
   * content changes rarely, the response is public and carries an ETag so the
   * Vue SPA and the Flutter app can revalidate cheaply instead of downloading
   * it on every launch.
   */
  public function show(Request $request)
  {
    $page = Page::query()
      ->where('slug', 'about')
      ->where('is_published', true)
      ->with(['sections', 'images'])
      ->first();

    if (! $page) {
      return response()->json([
        'message' => 'About page not found, try again later or contact support.',
        'status' => 404,
      ], 404);
    }

    $response = (new AboutResource($page))->response($request);

    // ETag is derived from the rendered payload, so it already varies by lang.
    $response->setEtag(md5($response->getContent()));
    $response->setPublic();
    $response->setMaxAge(300);
    $response->isNotModified($request);

    return $response;
  }
}
