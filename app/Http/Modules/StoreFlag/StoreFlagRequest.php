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
      'store_chain_id' => 'required|exists:store_chains,id',
      'name'           => ['required', 'string', 'max:150',
        Rule::unique('store_flags', 'name')
          ->where('store_chain_id', $this->get('store_chain_id')),
      ],
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = [
        'required', 
        'string',
        'max:150',
        Rule::unique('store_flags', 'name')
          ->where('store_chain_id', $this->get('store_chain_id'))
          ->whereNot('id', $this->store_flag->id),
      ];
    }

    return $rules;
  }
  
}
