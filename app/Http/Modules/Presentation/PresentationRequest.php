<?php

namespace App\Http\Modules\Presentation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PresentationRequest extends FormRequest
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
            'product_id'            => 'required|exists:products,id',
            'price'                 => 'required|integer|min:0',
            'is_minimal_expression' => 'required|boolean',
            'units'                 => 'required|numeric|min:0',
            'description'           => [
              'required', 
              'string',
              'max:150',
              Rule::unique('presentations', 'description')
                ->where(function ($query) {
                  return $query->where('product_id', $this->get('product_id'));
                }),
            ], 
        ];

        if ($this->isMethod('PUT')) {
            $rules['description'] = [
              'required', 
              'string',
              'max:150',
              Rule::unique('presentations', 'description')
                ->where(function ($query) {
                  return $query->where('product_id', $this->get('product_id'))
                    ->where('id', '!=', $this->presentation->id);
                }),
            ];
        }

        return $rules;
    }
}
