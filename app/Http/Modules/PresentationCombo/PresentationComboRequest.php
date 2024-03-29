<?php

namespace App\Http\Modules\PresentationCombo;

use App\Rules\IUniqueRule;
use App\Http\Modules\Turn\Turn;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Modules\Presentation\Presentation;

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
      'suggested_price'          => 'required|numeric|min:0',
      'presentations'            => ['required','array',$this->oneCompanyRule()],
      'presentations.*'          => 'integer|visible_through_company:presentations',
      'prices'                   => 'required|array',
      'prices.*.suggested_price' => 'required|numeric|min:0',
      'prices.*.turns'           => 'required|array',
      'description'              => ['required', 'string', 'max:255',
        (new IUniqueRule('presentation_combos'))
          ->whereIn('company_id', [0, auth()->user()->company_id])
          ->ignore($this->presentation_combo),
      ],
    ];

    foreach($this->get('prices', []) as $indexPrice => $price) {

      $rules["prices.$indexPrice.store_id"] = 'required|integer|store_visible';

      foreach($price['turns'] ?? [] as $indexTurn => $turn_id) {

        $rules["prices.$indexPrice.turns.$indexTurn"] = ['integer',
          function ($attribute, $value, $fail) use ($price, $turn_id) {
            $turn = Turn::where('id', $turn_id)
              ->where('store_id', $price['store_id'] ?? -1)
              ->first();

            if (!$turn) {
              $fail("El campo {$attribute} es inválido.");
            }
          },
        ];
      }
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

  private function oneCompanyRule()
  {
    return function ($attribute, $value, $fail) {
      $companies = Presentation::query()
        ->whereIn('id', collect($value))
        ->whereNotIn('company_id', [0])
        ->pluck('company_id')
        ->unique()
        ->count();
      
      if ($companies > 1) {
        $fail("El campo {$attribute} contiene presentaciones de múltiples empresas.");
      }
    };
  }
  
}
