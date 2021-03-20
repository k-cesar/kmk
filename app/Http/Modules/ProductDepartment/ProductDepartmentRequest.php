<?php

namespace App\Http\Modules\ProductDepartment;

use Illuminate\Foundation\Http\FormRequest;

class ProductDepartmentRequest extends FormRequest
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
      'name' => 'required|string|max:255|iunique:product_departments,name,'.($this->product_department->id ?? ''),
    ];

    return $rules;
  }
  
}
