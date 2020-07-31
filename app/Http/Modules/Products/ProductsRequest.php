<?php

namespace App\Http\Modules\Products;

use Illuminate\Foundation\Http\FormRequest;

class ProductsRequest extends FormRequest
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
            'description' => 'string|max:255|unique:products',
            'is_all_countries' => 'required|boolean',
            'brand_id' => 'required|exists:brands,id',
            'product_category_id' => 'required|exists:product_categories,id',
            'product_subcategory_id' => 'required|exists:product_subcategories,id',
            'is_taxable' => 'required|boolean',
            'is_inventoriable' => 'required|boolean',
            'uom_id' => 'required|exists:uoms,id',
            'minimal_expresion' => 'required|string',
            'suggested_price' => 'required|string|min:2',
        ];

        if ($this->isMethod('PUT')) {
            //$rules['name'] = "required|string|max:255|unique:product_categories,name,\"{$this->product_category->name}\",name";
            $rules['description'] = "string|max:255|unique:products,description,\"{$this->products->description}\",description";
        }

        
        return $rules;
    }
}
