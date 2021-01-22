<?php

namespace App\Http\Modules\Region;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
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
      'country_id' => 'required|exists:countries,id',
      'name'       => ['required', 'string','max:150',
        Rule::unique('regions', 'name')
          ->where('country_id', $this->get('country_id')),
      ],
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = ['required', 'string','max:150',
        Rule::unique('regions', 'name')
          ->where('country_id', $this->get('country_id'))
          ->whereNot('id', $this->region->id),
      ];
    }

    return $rules;
  }
  
}
