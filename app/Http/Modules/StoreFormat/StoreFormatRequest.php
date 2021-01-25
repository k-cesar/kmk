<?php

namespace App\Http\Modules\StoreFormat;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormatRequest extends FormRequest
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
      'name' => 'required|string|max:150|unique:store_formats'.($this->store_format ? ",{$this->store_format->id}" : ''),
    ];

    return $rules;
  }
  
}
