<?php

namespace App\Http\Modules\Company;

use App\Rules\IUniqueRule;
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
      'phone'                 => 'required|digits_between:1,50|unique:companies,phone'.($this->company ? ",{$this->company->id}" : ''),
      'reason'                => 'required|string|max:1000',
      'regime'                => 'sometimes|nullable|string|max:50',
      'address'               => 'required|string|max:255',
      'currency_id'           => 'required|integer|exists:currencies,id,deleted_at,NULL',
      'country_id'            => 'required|integer|exists:countries,id,deleted_at,NULL',
      'nit'                   => ['required', 'string', 'alpha_num', 'max:15',
        (new IUniqueRule('companies'))
          ->where('country_id', $this->get('country_id'))
          ->ignore($this->company),
      ],
    ];

    if (auth()->user()->role->level < 2) {
      if ($this->isMethod('POST') || !$this->company->allow_fel) {
        $rules['allow_fel'] = 'required|boolean';
      }
      
      $rules['allow_add_users']    = 'required|boolean';
      $rules['allow_add_stores']   = 'required|boolean';
      $rules['allow_add_products'] = 'required|boolean';
    }

    return $rules;
  }
  
}
