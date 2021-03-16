<?php

namespace App\Http\Modules\Uom;

use Illuminate\Foundation\Http\FormRequest;

class UomRequest extends FormRequest
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
      'name'         => 'required|string|max:255|iunique:uoms,name,'.($this->uom->id ?? ''),
      'abbreviation' => 'required|string|max:16|iunique:uoms,abbreviation,'.($this->uom->id ?? ''),
      'description'  => 'sometimes|nullable|string|max:500',
    ];

    return $rules;
  }
  
}
