<?php

namespace App\Http\Modules\State;

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
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    $rules = [
      'region_id' => 'required|exists:regions,id',
      'name'       => ['required', 'string', 'max:150',
        Rule::unique('states', 'name')
          ->where('region_id', $this->get('region_id')),
      ],
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = ['required', 'string', 'max:150',
        Rule::unique('states', 'name')
          ->where('region_id', $this->get('region_id'))
          ->whereNot('id', $this->state->id),
      ];
    }

    return $rules;
  }
  
}
