<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * JSON bearer-token authentication (Sanctum).
 *
 * Register and login are public and issue a personal access token; the client
 * sends it as `Authorization: Bearer <token>` on every protected call. Logout
 * revokes the token it was called with, so signing out of one device leaves the
 * others signed in.
 */
class AuthController extends Controller
{
  /**
   * Create a customer account and sign it in.
   *
   * New accounts always get the `customer` role: admins are provisioned by
   * seeding/another admin, never by self-registration.
   */
  public function register(RegisterRequest $request): JsonResponse
  {
    $user = User::create($request->safe()->only(['name', 'email', 'password']));

    $role = Role::firstOrCreate(['name' => 'customer']);
    $user->roles()->syncWithoutDetaching([$role->id]);

    return $this->respondWithToken($user, $request->validated('device_name'), 201);
  }

  /**
   * Exchange email + password for a personal access token.
   *
   * A single generic message is returned for both "unknown email" and "wrong
   * password" so the endpoint cannot be used to enumerate accounts.
   */
  public function login(LoginRequest $request): JsonResponse
  {
    $user = User::where('email', $request->validated('email'))->first();

    if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
      return response()->json([
        // Same body for unknown email and wrong password; translated via
        // lang/{en,ar}/auth.php, so ?lang=ar answers in Arabic.
        'message' => __('auth.invalid_credentials'),
        'status' => 401,
      ], 401);
    }

    return $this->respondWithToken($user, $request->validated('device_name'), 200);
  }

  /**
   * Revoke the token used for this request (sign out of this device only).
   */
  public function logout(Request $request): Response
  {
    $token = $request->user()->currentAccessToken();

    // A session-authenticated request carries a transient token that has no
    // database row to delete; bearer-token clients get their token revoked.
    if ($token instanceof PersonalAccessToken) {
      $token->delete();
    }

    return response()->noContent();
  }

  /**
   * The authenticated user, with role names (used by the admin UI).
   */
  public function me(Request $request): UserResource
  {
    return new UserResource($request->user()->loadMissing('roles'));
  }

  /**
   * Issue a token for the user and return it with the public user shape.
   *
   * The plain-text token is shown exactly once — only its hash is stored, so a
   * client that loses it has to log in again.
   */
  private function respondWithToken(User $user, ?string $deviceName, int $status): JsonResponse
  {
    $token = $user->createToken($deviceName ?: 'api')->plainTextToken;

    return response()->json([
      'data' => [
        'user' => new UserResource($user->loadMissing('roles')),
        'token' => $token,
        'tokenType' => 'Bearer',
      ],
    ], $status);
  }
}
