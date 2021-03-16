<?php

namespace App\Http\Modules\Zone;

use App\Rules\IUniqueRule;
use Illuminate\Foundation\Http\FormRequest;

class ZoneRequest extends FormRequest
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
      'municipality_id' => 'required|integer|exists:municipalities,id,deleted_at,NULL',
      'name'            => ['required', 'string', 'max:150',
        (new IUniqueRule('zones'))
          ->where('municipality_id', $this->get('municipality_id'))
          ->ignore($this->zone),
      ],
    ];

    return $rules;
  }
  
}
