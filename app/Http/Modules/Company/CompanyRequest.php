<?php

namespace App\Http\Modules\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
      'nit'               => 'required|string|max:50',
      'name'              => 'required|string|max:100',
      'comercial_name'    => 'required|string|max:255',
      'comercial_address' => 'required|string|max:255',
      'active'            => 'required|string|in:'.implode(',', Company::getActiveOptions()),
      'currency_id'       => 'required|integer'
    ];

    return $rules;
  }
  
}
