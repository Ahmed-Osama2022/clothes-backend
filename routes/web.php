<?php

use App\Http\Controllers\Docs\ApiDocsController;
use Illuminate\Support\Facades\Route;

// Framework skeleton page — left untouched, this is a backend-only project.
Route::get('/', function () {
  return view('welcome');
});

// Human-readable API reference (see docs/api/v1.php for the content).
Route::get('/docs/api', [ApiDocsController::class, 'index'])->name('docs.api');

Route::redirect('/docs', '/docs/api');
