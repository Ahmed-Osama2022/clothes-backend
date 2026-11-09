<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    // API-only backend: there is no login page. Without this, the default
    // callback calls route('login') and an unauthenticated request fails
    // with "Route [login] not defined" instead of a clean 401.
    $middleware->redirectGuestsTo(fn(Request $request) => $request->is('api/*') ? null : '/');

    // Bilingual errors: ?lang=en|ar selects the locale before validation and
    // authorization run, so 422/401 bodies answer in the same language as the
    // content endpoints instead of always using APP_LOCALE.
    $middleware->api(prepend: [SetLocale::class]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    // Everything under /api answers JSON, even when a client omits the
    // Accept header, so errors keep the { message, status } shape.
    $exceptions->shouldRenderJsonWhen(
      fn(Request $request) => $request->is('api/*') || $request->expectsJson()
    );

    $exceptions->render(function (Throwable $e, Request $request) {
      if (! $request->is('api/*')) {
        return null;
      }

      $status = $e instanceof HttpExceptionInterface
        ? $e->getStatusCode()
        : 500;

      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
        'status' => $status,
      ], $status);
    });

    // Laravel's default 401 body is {"message":"Unauthenticated."}. Every
    // other error in this API uses { message, status }, so the auth guard
    // would be the odd one out. Returning null falls back to the framework
    // behaviour for non-API requests (web routes still redirect to /).
    $exceptions->render(function (AuthenticationException $e, Request $request) {
      if (! $request->is('api/*')) {
        return null;
      }

      return response()->json([
        // Translated: lang/{en,ar}/auth.php, following the ?lang parameter.
        'message' => __('auth.unauthenticated'),
        'status' => 401,
      ], 401);
    });
  })->create();
