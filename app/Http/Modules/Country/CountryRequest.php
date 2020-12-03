<?php

namespace App\Http\Modules\Country;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
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
      'name'        => 'required|string|max:150|unique:countries',
      'currency_id' => 'required|exists:currencies,id'
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = "required|string|max:150|unique:countries,name,{$this->country->id}";
    }

    return $rules;
  }
  
}
