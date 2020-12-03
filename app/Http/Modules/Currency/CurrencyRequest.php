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
      'name'         => 'required|string|max:255|unique:currencies',
      'symbol'       => 'required|string|max:2',
      'abbreviation' => 'required|string|max:16|unique:currencies',
      'description'  => 'sometimes|string|max:500|nullable',
      'disabled'     => 'required|boolean',
    ];

    if ($this->isMethod('PUT')) {
      $rules['name']         = "required|string|max:255|unique:currencies,name,{$this->currency->id}";
      $rules['abbreviation'] = "required|string|max:16|unique:currencies,abbreviation,{$this->currency->id}";
    }

    return $rules;
  }
  
}
