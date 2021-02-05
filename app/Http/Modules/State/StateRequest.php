<?php

namespace App\Http\Modules\State;

use App\Support\Helper;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StateRequest extends FormRequest
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
      'region_id' => 'required|integer|exists:regions,id,deleted_at,NULL',
      'name'       => ['required', 'string', 'max:150',
        Rule::unique('states')
          ->where('region_id', $this->get('region_id'))
          ->ignore($this->state),
      ],
    ];

    return $rules;
  }
  
}
