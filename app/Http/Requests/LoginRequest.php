<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Credentials for the token exchange. Only shape is validated here; whether the
 * pair is correct is decided in the controller (401), not by a validation rule,
 * so a wrong password never reads like a malformed request.
 *
 * Messages come from lang/{en,ar}/validation.php ("custom" block), so the
 * response language follows the ?lang parameter.
 */
class LoginRequest extends FormRequest
{
  /**
   * Logging in is open to everyone.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Same normalization as registration, so login works whatever the client's
   * keyboard produced.
   */
  protected function prepareForValidation(): void
  {
    if ($this->has('email')) {
      $this->merge(['email' => strtolower(trim((string) $this->input('email')))]);
    }
  }

  /**
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    return [
      'email' => ['required', 'string', 'email'],
      'password' => ['required', 'string'],
      'device_name' => ['sometimes', 'nullable', 'string', 'max:100'],
    ];
  }
}
