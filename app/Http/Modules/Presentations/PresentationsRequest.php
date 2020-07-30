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
            'name' => 'string|max:255|unique:product_presentations', 
        ];
        return $rules;
    }
}
