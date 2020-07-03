<?php

namespace App\Http\Modules\PaymentMethod;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodRequest extends FormRequest
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
      'company_id' => 'required|exists:companies,id',
      'name'     => [
        'required', 
        'string',
        'max:150',
        Rule::unique('payment_methods', 'name')
          ->where(function ($query) {
            return $query->where('company_id', $this->get('company_id'));
          }),
      ],
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = [
        'required', 
        'string',
        'max:150',
        Rule::unique('payment_methods', 'name')
          ->where(function ($query) {
            return $query->where('company_id', $this->get('company_id'))
              ->where('id', '!=', $this->payment_method->id);
          }),
      ];
    }

    return $rules;
  }
  
}
