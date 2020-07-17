<?php

namespace App\Http\Modules\Municipality;

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
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    $rules = [
      'state_id' => 'required|exists:states,id',
      'name'     => [
        'required', 
        'string',
        'max:150',
        Rule::unique('municipalities', 'name')
          ->where(function ($query) {
            return $query->where('state_id', $this->get('state_id'));
          }),
      ],
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = [
        'required', 
        'string',
        'max:150',
        Rule::unique('municipalities', 'name')
          ->where(function ($query) {
            return $query->where('state_id', $this->get('state_id'))
              ->where('id', '!=', $this->municipality->id);
          }),
      ];
    }

    return $rules;
  }
  
}
