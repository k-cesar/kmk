<?php

namespace App\Http\Modules\Product;

use Illuminate\Validation\Rule;
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
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        return array_merge($this->all(), ['presentation_description' => $this->get('description')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'is_all_countries'       => 'required|boolean',
            'brand_id'               => 'required|exists:brands,id',
            'product_category_id'    => 'required|exists:product_categories,id',
            'product_subcategory_id' => 'required|exists:product_subcategories,id',
            'is_taxable'             => 'required|boolean',
            'is_inventoriable'       => 'required|boolean',
            'uom_id'                 => 'required|exists:uoms,id',
            'suggested_price'        => 'required|numeric|min:0',
            'countries'              => 'required|array',
            'countries.*'            => 'exists:countries,id',
            'description'            => ['required', 'string', 'max:255',
              Rule::unique('products', 'description')
                ->whereIn('company_id', [0, auth()->user()->company_id]),
            ],
        ];

        if ($this->isMethod('POST')) {
            $rules['presentation_description'] = [
              Rule::unique('presentations', 'description')
                ->whereIn('company_id', [0, auth()->user()->company_id]),
            ];
        } else {
            $rules['description'] = ['required', 'string','max:255',
              Rule::unique('products', 'description')
                ->whereIn('company_id', [0, auth()->user()->company_id])
                ->whereNot('id', $this->product->id),
            ];
        }

        return $rules;
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated()
    {
        $validatedData = parent::validated();

        if ($this->isMethod('POST')) {
            $validatedData['company_id'] = auth()->user()->company_id;
        }

        return $validatedData;
    }
}
