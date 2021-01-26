<?php

namespace App\Http\Modules\StoreType;

use Illuminate\Foundation\Http\FormRequest;

class StoreTypeRequest extends FormRequest
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
      'name' => 'required|string|max:150|unique:store_types,name'.($this->store_type ? ",{$this->store_type->id}" : ''),
    ];

    return $rules;
  }
  
}
