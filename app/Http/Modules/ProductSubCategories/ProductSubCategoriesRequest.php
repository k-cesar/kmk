<?php

namespace App\Http\Modules\ProductSubCategories;

use Illuminate\Foundation\Http\FormRequest;

class ProductSubCategoriesRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'product_category_id'           => 'required|exists:product_categories,id'
        ];

        if ($this->isMethod('PUT')) {
            $rules['name'] = "required|string|max:255";
            $rules['product_category_id'] = "exists:product_categories,id";
        }

        if ($this->isMethod('GET')) {     
            $rules = [
              'name' => 'string|max:255',       
            ];
        }
        return $rules;
    }
}
