<?php

namespace App\Http\Modules\Product;

use App\Support\Helper;
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'description'              => Helper::strToUpper($this->description),
            'presentation_description' => Helper::strToUpper($this->description)
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
            'is_all_countries'       => 'required|boolean',
            'brand_id'               => 'required|integer|exists:brands,id,deleted_at,NULL',
            'product_category_id'    => 'required|integer|exists:product_categories,id,deleted_at,NULL',
            'product_subcategory_id' => 'required|integer|exists:product_subcategories,id,deleted_at,NULL',
            'is_taxable'             => 'required|boolean',
            'is_inventoriable'       => 'required|boolean',
            'uom_id'                 => 'required|integer|exists:uoms,id,deleted_at,NULL',
            'suggested_price'        => 'required|numeric|min:0',
            'countries'              => 'required|array',
            'countries.*'            => 'integer|exists:countries,id,deleted_at,NULL',
            'description'            => ['required', 'string', 'max:255',
              Rule::unique('products')
                ->whereIn('company_id', [0, auth()->user()->company_id])
                ->ignore($this->product),
            ],
        ];

        if ($this->isMethod('POST')) {
            $rules['presentation_description'] = [
              Rule::unique('presentations', 'description')
                ->whereIn('company_id', [0, auth()->user()->company_id]),
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
