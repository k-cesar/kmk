<?php

namespace App\Http\Modules\Deposit;

use Illuminate\Validation\Rule;
use App\Http\Modules\Store\Store;
use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
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
      'deposit_number'  => 'required|string|max:100|unique:deposits',
      'amount'          => 'required|numeric|min:0',
      'images'          => 'required|array',
      'images.*.title'  => 'required|string|max:255|distinct',
      'images.*.base64' => 'required|string',
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
      'store_turn_id'   => [
        'required',
        Rule::exists('store_turns', 'id')
          ->where(function ($query) {
            return $query->where('id', $this->get('store_turn_id'))
              ->where('store_id', $this->get('store_id'))
              ->where('is_open', true);
          }),
      ]
    ];

    if ($this->isMethod('PUT')) {
      $rules['deposit_number'] = "required|string|max:100|unique:deposits,deposit_number,{$this->deposit->id}";
    }

    return $rules;
  }
}
