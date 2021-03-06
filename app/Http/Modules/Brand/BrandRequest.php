<?php

namespace App\Http\Modules\Brand;

use App\Support\Helper;
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
      'maker_id' => 'required|integer|exists:makers,id,deleted_at,NULL',
      'name'     => ['required', 'string', 'max:150',
        Rule::unique('brands')
          ->where('maker_id', $this->get('maker_id'))
          ->ignore($this->brand),
      ],
    ];

    return $rules;
  }
  
}
