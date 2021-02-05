<?php

namespace App\Http\Modules\Country;

use App\Support\Helper;
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
   * Prepare the data for validation.
   *
   * @return void
   */
  protected function prepareForValidation()
  {
    $this->merge([
      'name' => Helper::strToUpper($this->name)
    ]);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    $rules = [
      'name'        => 'required|string|max:150|unique:countries,name'.($this->country ? ",{$this->country->id}" : ''),
      'currency_id' => 'required|integer|exists:currencies,id,deleted_at,NULL',
    ];

    return $rules;
  }
  
}
