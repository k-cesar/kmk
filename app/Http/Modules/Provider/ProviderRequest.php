<?php

namespace App\Http\Modules\Provider;

use App\Rules\IUniqueRule;
use Illuminate\Foundation\Http\FormRequest;

class ProviderRequest extends FormRequest
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
      'country_id' => 'required|integer|exists:countries,id,deleted_at,NULL',
      'company_id' => 'sometimes|integer|exists:companies,id',
      'name'       => ['required', 'string', 'max:150',
        (new IUniqueRule('providers'))
          ->where('country_id', $this->get('country_id'))
          ->whereIn('company_id', [0, $this->get('company_id')])
          ->ignore($this->provider),
      ],
      'nit'        => ['required', 'string', 'alpha_num', 'max:15',
        (new IUniqueRule('providers'))
          ->where('country_id', $this->get('country_id'))
          ->whereIn('company_id', [0, $this->get('company_id')])
          ->ignore($this->provider),
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

    $validatedData['company_id'] = auth()->user()->company_id;

    return $validatedData;
  }
  
}
