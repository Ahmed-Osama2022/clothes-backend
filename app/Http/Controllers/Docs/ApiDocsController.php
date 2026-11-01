<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Serves the human-readable API reference (the only web page besides the
 * framework skeleton welcome view). Content lives in docs/api/v1.php so each
 * endpoint's contract is versioned next to the code, not hidden in a package.
 */
class ApiDocsController extends Controller
{
  /**
   * Render the API reference for the current version.
   */
  public function index(): View
  {
    // require (not config()) keeps the spec out of config/ and out of the
    // config cache, which is what we want for docs that change with the API.
    $spec = require base_path('docs/api/v1.php');

    return view('docs.api', ['spec' => $spec]);
  }
}
