<?php

namespace App\Http\Modules\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'description'            => 'string|max:255|unique:products',
            'is_all_countries'       => 'required|boolean',
            'brand_id'               => 'required|exists:brands,id',
            'product_category_id'    => 'required|exists:product_categories,id',
            'product_subcategory_id' => 'required|exists:product_subcategories,id',
            'is_taxable'             => 'required|boolean',
            'is_inventoriable'       => 'required|boolean',
            'uom_id'                 => 'required|exists:uoms,id',
            'minimal_expresion'      => 'required|string',
            'suggested_price'        => 'required|numeric|min:0',
        ];

        if ($this->isMethod('PUT')) {
            $rules['description'] = "string|max:255|unique:products,description,\"{$this->product->description}\",description";
        }

        return $rules;
    }
}
