<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Validates self-service sign-up. `device_name` is optional metadata for the
 * issued token (e.g. "iPhone 15"), not part of the account.
 *
 * `password` must be sent twice: `password` + `password_confirmation`. The
 * `confirmed` rule checks they are identical, which catches a typo before the
 * account is created (the password cannot be read back afterwards).
 *
 * Messages come from lang/{en,ar}/validation.php ("custom" block), so the
 * response language follows the ?lang parameter.
 */
class RegisterRequest extends FormRequest
{
  /**
   * Registration is open to everyone.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Emails are matched case-insensitively, so trim and lowercase them before
   * validation instead of rejecting "Sara@Example.com" with a format error.
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
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
      // "confirmed" requires a matching "password_confirmation" field; a
      // missing or different value fails here, before the account is created.
      'password' => ['required', 'string', 'confirmed', Password::defaults()],
      'device_name' => ['sometimes', 'nullable', 'string', 'max:100'],
    ];
  }
}
