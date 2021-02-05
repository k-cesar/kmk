<?php

namespace App\Http\Modules\Municipality;

use App\Support\Helper;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MunicipalityRequest extends FormRequest
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
      'state_id' => 'required|integer|exists:states,id,deleted_at,NULL',
      'name'     => ['required', 'string', 'max:150',
        Rule::unique('municipalities')
          ->where('state_id', $this->get('state_id'))
          ->ignore($this->municipality),
      ],
    ];

    return $rules;
  }
  
}
