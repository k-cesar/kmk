<?php

namespace App\Http\Modules\Currency;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
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
      'symbol'        => 'required|string|max:10',
      'description'   => 'required|string|max:20',
      'active'        => 'required|string|in:'.implode(',', Currency::getActiveOptions()),
      'main_currency' => 'required|string|in:'.implode(',', Currency::getMainCurrencyOptions()),
    ];

    return $rules;
  }
  
}
