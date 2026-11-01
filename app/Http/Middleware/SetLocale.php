<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the ?lang= query parameter to the application locale.
 *
 * The API is bilingual (EN/AR). Model resources resolve their columns per
 * request, but validation errors, auth messages and every other translated
 * string are produced before the controller runs — without this middleware they
 * would always use APP_LOCALE. Only the supported locales are accepted; any
 * other value leaves the configured default untouched (no 500s on garbage).
 */
class SetLocale
{
  /**
   * Locales the API ships translations for.
   *
   * @var array<int, string>
   */
  public const SUPPORTED = ['en', 'ar'];

  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next): Response
  {
    $locale = $request->query('lang');

    if (is_string($locale) && in_array($locale, self::SUPPORTED, true)) {
      app()->setLocale($locale);
    }

    return $next($request);
  }
}
