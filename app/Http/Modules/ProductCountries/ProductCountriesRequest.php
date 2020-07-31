<?php

namespace App\Http\Modules\ProductCountries;

use Illuminate\Foundation\Http\FormRequest;

class ProductCountriesRequest extends FormRequest
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
            'product_id' => 'required|integer|exists:products,id',
            'country_id' => 'required|integer|exists:countries,id'
        ];

        if ($this->isMethod('PUT')) {
            $rules['product_id'] = "required|integer|exists:products,id";
            $rules['country_id'] = "required|integer|exists:countries,id";
        }

        return $rules;
    }
}
