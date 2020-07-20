<?php

namespace App\Http\Modules\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;

class ProductCategoryRequest extends FormRequest
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
      'name' => 'required|string|max:255|unique:product_categories',
      'product_department_id'           => 'required|exists:product_departments,id'
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = "required|string|max:255|unique:product_categories,name,\"{$this->product_category->name}\",name";
    }

    return $rules;
  }
  
}
