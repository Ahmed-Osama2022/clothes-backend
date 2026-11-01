<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
  /**
   * Checkout is open to everyone: guests may order (handoff §5) and an
   * authenticated caller simply gets the order linked to their account.
   * Authorization of the *contents* happens below and in the controller.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Only ids and quantities are accepted. Prices are deliberately not part of
   * the contract — they are snapshotted from the database at checkout time, so
   * a client can never influence what it pays.
   *
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    return [
      'items' => ['required', 'array', 'min:1'],
      'items.*.product_variant_id' => [
        'required',
        'integer',
        'distinct',
        Rule::exists('product_variants', 'id'),
      ],
      'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
    ];
  }

  /**
   * Make the one non-obvious rule readable, since a duplicated variant would
   * otherwise silently split into two lines and break the stock check.
   *
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'items.*.product_variant_id.distinct' => 'Each variant may appear only once; merge the quantities into one line.',
    ];
  }
}
