<?php

namespace App\Http\Modules\ProductSubcategory;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductSubcategoryRequest extends FormRequest
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
            'product_category_id' => 'required|exists:product_categories,id',
            'name'                => ['required', 'string', 'max:150',
                Rule::unique('product_subcategories', 'name')
                    ->where('product_category_id', $this->get('product_category_id')),
            ],
        ];

        if ($this->isMethod('PUT')) {
            $rules['name'] = ['required', 'string', 'max:150',
                Rule::unique('product_subcategories', 'name')
                    ->where('product_category_id', $this->get('product_category_id'))
                    ->whereNot('id', $this->product_subcategory->id),
            ];
        }

        return $rules;
    }
}
