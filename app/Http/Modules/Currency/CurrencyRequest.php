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
      'name'         => 'required|string|max:150|unique:currencies,name'.($this->currency ? ",{$this->currency->id}" : ''),
      'abbreviation' => 'required|string|max:16|unique:currencies,abbreviation'.($this->currency ? ",{$this->currency->id}" : ''),
      'symbol'       => 'required|string|max:2',
      'description'  => 'sometimes|string|nullable|max:500',
      'disabled'     => 'required|boolean',
    ];

    return $rules;
  }
  
}
