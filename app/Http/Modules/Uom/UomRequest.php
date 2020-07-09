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
      'name'         => 'required|string|max:255|unique:uoms',
      'abbreviation' => 'required|string|max:16|unique:uoms',
      'description'  => 'sometimes|nullable|string|max:500',
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = "required|string|max:255|unique:uoms,name,\"{$this->uom->name}\",name";
      $rules['abbreviation'] = "required|string|max:16|unique:uoms,abbreviation,\"{$this->uom->abbreviation}\",abbreviation";
    }

    return $rules;
  }
  
}
