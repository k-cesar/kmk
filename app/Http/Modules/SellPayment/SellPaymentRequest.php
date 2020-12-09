<?php

namespace App\Http\Modules\SellPayment;

use App\Http\Modules\Sell\Sell;
use Illuminate\Validation\Rule;
use App\Http\Modules\Store\Store;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Modules\PaymentMethod\PaymentMethod;

class SellPaymentRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    $rules = [
      'payment_method_id' => 'required|exists:payment_methods,id|not_in:'.PaymentMethod::where('name', PaymentMethod::OPTION_PAYMENT_CREDIT)->first()->id,
      'description'       => 'sometimes|nullable|string|max:250',
      'store_id'   => [
        'required',
        'integer',
        function ($attribute, $value, $fail) {
          $store = Store::where('id', $value)
            ->visible(auth()->user())
            ->first();

          if (!$store) {
            $fail("El campo {$attribute} es inválido.");
          }
        },
      ],
      'store_turn_id'            => [
        'required',
        Rule::exists('store_turns', 'id')
          ->where(function ($query) {
            return $query->where('id', $this->get('store_turn_id'))
              ->where('store_id', $this->get('store_id'))
              ->where('is_open', true);
        }),
      ]
    ];
    
    if ($this->sell_payment->sell->status != Sell::OPTION_STATUS_PENDING) {
      abort(404);
    }

    return $rules;
  }
}