<?php

namespace App\Http\Modules\ProductCategory;

use App\Support\Helper;
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
      'name'                  => 'required|string|max:255|unique:product_categories,name'.($this->product_category ? ",{$this->product_category->id}" : ''),
      'product_department_id' => 'required|integer|exists:product_departments,id,deleted_at,NULL',
    ];

    return $rules;
  }
  
}
