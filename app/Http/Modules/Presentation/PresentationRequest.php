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
      'price'                 => 'required|numeric|min:0',
      'is_grouping'           => 'required|boolean',
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

    if ($this->get('is_grouping')) {
      $rules['units'] = 'required|numeric|min:2';
    }

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

  /**
   * Get the validated data from the request.
   *
   * @return array
   */
  public function validated()
  {
    $validatedData = parent::validated();

    if ( ! $validatedData['is_grouping'] ) {
      $validatedData['units'] = 1;
    }

    return $validatedData;
  }
}
