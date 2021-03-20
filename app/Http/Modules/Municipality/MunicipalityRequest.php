<?php

namespace App\Http\Modules\Municipality;

use App\Rules\IUniqueRule;
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
      'state_id' => 'required|integer|exists:states,id,deleted_at,NULL',
      'name'     => ['required', 'string', 'max:150',
        (new IUniqueRule('municipalities'))
          ->where('state_id', $this->get('state_id'))
          ->ignore($this->municipality),
      ],
    ];

    return $rules;
  }
  
}
