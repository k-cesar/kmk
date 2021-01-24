<?php

namespace App\Http\Modules\StoreFlag;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFlagRequest extends FormRequest
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
      'store_chain_id' => 'required|integer|exists:store_chains,id,deleted_at,NULL',
      'name'           => ['required', 'string', 'max:150',
        Rule::unique('store_flags')
          ->where('store_chain_id', $this->get('store_chain_id'))
          ->ignore($this->store_flag),
      ],
    ];

    return $rules;
  }
  
}
