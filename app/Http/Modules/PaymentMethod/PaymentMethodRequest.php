<?php

namespace App\Http\Modules\PaymentMethod;

use App\Support\Helper;
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
      'company_id' => 'required|integer|exists:companies,id,deleted_at,NULL',
      'name'       => ['required', 'string', 'max:150',
        Rule::unique('payment_methods')
          ->where('company_id', $this->get('company_id'))
          ->ignore($this->payment_method),
      ],
    ];

    return $rules;
  }

  /**
   * Get the validated data from the request.
   *
   * @return array
   */
  public function validated()
  {
    $validatedData = parent::validated();

    if (auth()->user()->role->level > 1) {
      $validatedData['company_id'] = auth()->user()->company_id;
    }

    return $validatedData;
  }
}
