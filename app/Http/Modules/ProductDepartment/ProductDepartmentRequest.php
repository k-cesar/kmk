<?php

namespace App\Http\Modules\ProductDepartment;

use App\Support\Helper;
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
   * Prepare the data for validation.
   *
   * @return void
   */
  protected function prepareForValidation()
  {
    $this->merge([
      'name' => Helper::strToUpper($this->name)
    ]);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    $rules = [
      'name' => 'required|string|max:255|unique:product_departments,name'.($this->product_department ? ",{$this->product_department->id}" : ''),
    ];

    return $rules;
  }
  
}
