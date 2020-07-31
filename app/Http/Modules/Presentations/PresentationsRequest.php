<?php

namespace App\Http\Modules\Presentations;

use Illuminate\Foundation\Http\FormRequest;

class PresentationsRequest extends FormRequest
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
            'price' => 'required|integer|min:2',
        ];

        if($this->isMethod('PUT')) {
            $rules['description'] = "required|string|max:150|unique:product_presentations,description,\"{$this->presentation->description}\",description";
        }

        return $rules;
    }
}
