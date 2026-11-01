<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

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
  })
  ->withExceptions(function (Exceptions $exceptions) {
    // Everything under /api answers JSON, even when a client omits the
    // Accept header, so errors keep the { message, status } shape.
    $exceptions->shouldRenderJsonWhen(
      fn(Request $request) => $request->is('api/*') || $request->expectsJson()
    );

    // Laravel's default 401 body is {"message":"Unauthenticated."}. Every
    // other error in this API uses { message, status }, so the auth guard
    // would be the odd one out. Returning null falls back to the framework
    // behaviour for non-API requests (web routes still redirect to /).
    $exceptions->render(function (AuthenticationException $e, Request $request) {
      if (! $request->is('api/*')) {
        return null;
      }

      return response()->json([
        'message' => 'Authentication required. Please login first',
        'status' => 401,
      ], 401);
    });
  })->create();
