<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
  /**
   * The policy check lives in the controller (OrderPolicy@update), which knows
   * the specific order being changed.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Only the status is mutable after checkout — items, the price snapshots and
   * the total are historical records and must not be rewritten.
   *
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    return [
      'status' => ['required', 'string', Rule::in(Order::STATUSES)],
    ];
  }
}
