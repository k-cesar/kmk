<?php

namespace App\Http\Modules\Region;

use App\Rules\IUniqueRule;
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
      'country_id' => 'required|integer|exists:countries,id,deleted_at,NULL',
      'name'       => ['required', 'string','max:150',
        (new IUniqueRule('regions'))
          ->where('country_id', $this->get('country_id'))
          ->ignore($this->region),
      ],
    ];

    return $rules;
  }
  
}
