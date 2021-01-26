<?php

namespace App\Http\Modules\StoreChain;

use Illuminate\Foundation\Http\FormRequest;

class StoreChainRequest extends FormRequest
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
      'name' => 'required|string|max:150|unique:store_chains,name'.($this->store_chain ? ",{$this->store_chain->id}" : ''),
    ];

    return $rules;
  }
  
}
