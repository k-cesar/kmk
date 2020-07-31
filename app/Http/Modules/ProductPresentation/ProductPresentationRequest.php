<?php

namespace App\Http\Modules\ProductPresentation;

use Illuminate\Foundation\Http\FormRequest;

class ProductPresentationRequest extends FormRequest
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
            'description' => 'required|string|max:150|unique:product_presentations',
            'price'       => 'required|integer|min:0',
        ];

        if ($this->isMethod('PUT')) {
            $rules['description'] = "required|string|max:150|unique:product_presentations,description,\"{$this->product_presentation->description}\",description";
        }

        return $rules;
    }
}
