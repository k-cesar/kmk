<?php

namespace App\Http\Modules\PresentationCombo;

use App\Http\Modules\Turn\Turn;
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
      'prices.*.turns'           => 'required|array',
    ];

    foreach($this->get('prices', []) as $indexPrice => $price) {

      $rules["prices.$indexPrice.store_id"] = 'required|integer|store_visible';

      foreach($price['turns'] ?? [] as $indexTurn => $turn_id) {

        $rules["prices.$indexPrice.turns.$indexTurn"] = [
          'integer',
          function ($attribute, $value, $fail) use ($price, $turn_id) {
            $turn = Turn::where('id', $turn_id)
              ->where('store_id', $price['store_id'] ?? -1)
              ->first();

            if (!$turn) {
              $fail("El campo {$attribute} {$turn_id} es inválido.");
            }
          },
        ];
      }
    }

    if ($this->isMethod('PUT')) {
      $rules['description'] = "required|string|max:150|unique:presentation_combos,description,{$this->presentation_combo->id}";
    }

    return $rules;
  }
  
}
