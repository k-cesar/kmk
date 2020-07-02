<?php

namespace App\Http\Modules\Maker;

use Illuminate\Foundation\Http\FormRequest;

class MakerRequest extends FormRequest
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
      'name' => 'required|string|max:150|unique:makers',
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = "required|string|max:150|unique:makers,name,{$this->maker->name},name";
    }

    return $rules;
  }
  
}
