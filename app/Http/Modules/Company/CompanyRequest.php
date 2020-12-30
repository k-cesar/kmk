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
      'regime'                => 'sometimes|nullable|string|max:50',
      'phone'                 => 'required|digits_between:1,50|unique:companies',
      'address'               => 'required|string|max:255',
      'currency_id'           => 'required|exists:currencies,id',
      'country_id'            => 'required|exists:countries,id',
      'nit'                   => ['required', 'string', 'max:15', 'regex:/^\d+k?$/i',
        Rule::unique('companies', 'nit')
          ->where(function ($query) {
            return $query->where('country_id', $this->get('country_id'));
          }),
      ],
    ];

    if ($this->isMethod('PUT')) {
      $rules['phone'] = "required|digits_between:1,50|unique:companies,phone,{$this->company->id}";

      $rules['nit'] = ['required', 'string', 'max:15', 'regex:/^\d+k?$/i',
        Rule::unique('companies', 'nit')
          ->where(function ($query) {
            return $query->where('country_id', $this->get('country_id'))
              ->where('id', '!=', $this->company->id);
          })
      ];
    }

    if (auth()->user()->role->level < 2) {
      if ($this->isMethod('POST') || !$this->company->allow_fel) {
        $rules['allow_fel'] = 'required|boolean';
      }
      
      $rules['allow_add_users'] = 'required|boolean';
      $rules['allow_add_stores']   = 'required|boolean';
      $rules['allow_add_products'] = 'required|boolean';
    }

    return $rules;
  }
  
}
