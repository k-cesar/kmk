<?php

namespace App\Http\Modules\SocioeconomicLevel;

use App\Support\Helper;
use Illuminate\Foundation\Http\FormRequest;

class SocioeconomicLevelRequest extends FormRequest
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
      'name'             => 'required|string|max:150|unique:socioeconomic_levels,name'.($this->socioeconomic_level ? ",{$this->socioeconomic_level->id}" : ''),
      'is_all_countries' => 'required|boolean',
      'countries'        => 'sometimes|array',
      'countries.*'      => 'integer|exists:countries,id,deleted_at,NULL',
    ];

    return $rules;
  }
  
}
