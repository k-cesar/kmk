<?php

namespace App\Http\Modules\Brand;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
      'maker_id' => 'required|exists:makers,id',
      'name'     => ['required', 'string', 'max:150',
        Rule::unique('brands', 'name')
          ->where('maker_id', $this->get('maker_id')),
      ],
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = ['required', 'string', 'max:150',
        Rule::unique('brands', 'name')
          ->where('maker_id', $this->get('maker_id'))
          ->whereNot('id', $this->brand->id),
      ];
    }

    return $rules;
  }
  
}
