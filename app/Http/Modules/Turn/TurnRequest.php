<?php

namespace App\Http\Modules\Turn;

use App\Support\Helper;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TurnRequest extends FormRequest
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
      'store_id'   => 'required|integer|store_visible',
      'start_time' => 'required|date_format:H:i:s',
      'end_time'   => 'required|date_format:H:i:s',
      'is_active'  => 'required|boolean',
      'is_default' => 'required|boolean',
      'name'       => ['required', 'string', 'max:255',
        Rule::unique('turns')
          ->where('store_id', $this->get('store_id'))
          ->ignore($this->turn),
      ],
    ];

    return $rules;
  }
  
}
