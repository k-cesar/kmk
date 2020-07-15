<?php

namespace App\Http\Modules\Company;

use Illuminate\Validation\Rule;
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
      'name'                  => 'required|string|max:255',
      'reason'                => 'required|string|max:1000',
      'phone'                 => 'required|digits_between:1,50|unique:companies',
      'address'               => 'required|string|max:255',
      'currency_id'           => 'required|exists:currencies,id',
      'country_id'            => 'required|exists:countries,id',
      'allow_add_products'    => 'required|boolean',
      'allow_add_stores'      => 'required|boolean',
      'is_electronic_invoice' => 'required|boolean',
      'uses_fel'              => 'required|boolean',
      'nit'                   => [
        'required', 
        'digits_between:1,15',
        Rule::unique('companies', 'nit')
          ->where(function ($query) {
            return $query->where('country_id', $this->get('country_id'));
          }),
      ],
    ];

    if ($this->isMethod('PUT')) {

      $rules['phone'] = "required|digits_between:1,50|unique:companies,phone,{$this->company->phone},phone";

      $rules['nit'] = [
        'required', 
        'digits_between:1,15',
        Rule::unique('companies', 'nit')
          ->where(function ($query) {
            return $query->where('country_id', $this->get('country_id'))
              ->where('id', '!=', $this->company->id);
          })
      ];
    }

    return $rules;
  }
  
}
