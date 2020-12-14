<?php

namespace App\Http\Modules\SellPayment;

use App\Http\Modules\Sell\Sell;
use Illuminate\Validation\Rule;
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
      'store_id'          => 'required|integer|store_visible',
      'payment_method_id' => 'required|integer|payment_method_visible|not_in:'.PaymentMethod::where('name', PaymentMethod::OPTION_PAYMENT_CREDIT)->first()->id,
      'description'       => 'sometimes|nullable|string|max:250',
      'store_turn_id'     => [
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