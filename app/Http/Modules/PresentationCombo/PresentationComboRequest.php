<?php

namespace App\Http\Modules\PresentationCombo;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PresentationComboRequest extends FormRequest
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
      'description'              => 'required|string|max:255|unique:presentation_combos',
      'uom_id'                   => 'required|exists:uoms,id',
      'minimal_expresion'        => 'required|string|max:255',
      'suggested_price'          => 'required|numeric|min:0',
      'presentations'            => 'required|array',
      'presentations.*'          => 'exists:presentations,id',
      'prices'                   => 'required|array',
      'prices.*.suggested_price' => 'required|numeric|min:0',
      'prices.*.store_id'        => 'required|exists:stores,id',
      'prices.*.turns'           => 'required|array',
      'prices.*.turns.*'         => 'exists:turns,id',
    ];

    if ($this->isMethod('PUT')) {
      $rules['description'] = "required|string|max:150|unique:presentation_combos,description,{$this->presentation_combo->id}";
    }

    return $rules;
  }
  
}
