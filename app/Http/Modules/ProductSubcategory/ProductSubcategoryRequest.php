<?php

namespace App\Http\Modules\ProductSubcategory;

use App\Rules\IUniqueRule;
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
            'product_category_id' => 'required|integer|exists:product_categories,id,deleted_at,NULL',
            'name'                => ['required', 'string', 'max:150',
                (new IUniqueRule('product_subcategories'))
                    ->where('product_category_id', $this->get('product_category_id'))
                    ->ignore($this->product_subcategory),
            ],
        ];

        return $rules;
    }
}
