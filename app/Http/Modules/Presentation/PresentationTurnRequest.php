<?php

namespace App\Http\Modules\Presentation;

use App\Http\Modules\Turn\Turn;
use Illuminate\Foundation\Http\FormRequest;

class PresentationTurnRequest extends FormRequest
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
    $this->allowedTurns = Turn::visibleThroughStore(auth()->user())
      ->pluck('id');

    $rules = [
      'apply_for_all'       => 'required|integer|in:0,1',
      'global_price'        => 'required_if:apply_for_all,1|numeric|min:0',
      'prices'              => 'required_if:apply_for_all,0|array',
      'prices.*.price'      => 'required|numeric|min:0',
      'prices.*.turns'      => 'required|array',
      'prices.*.turns.*.id' => "required|integer|distinct|in:{$this->allowedTurns->join(',')}",
    ];

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

    $validatedData['turnsPricesToSync'] = $this->getTurnsPricesToSync();

    return $validatedData;
  }

  /**
   * Get the turn prices to sync.
   *
   * @return array
   */
  private function getTurnsPricesToSync()
  {
    $turnsPrices = [];
      
    if ($this->get('apply_for_all')) {
      foreach ($this->allowedTurns as $allowedTurn) {
        $turnsPrices[] = [
          'id'    => $allowedTurn,
          'price' => $this->get('global_price')
        ];
      }
    } else {
      foreach ($this->get('prices', []) as $price) {
        foreach ($price['turns'] as $turn) {
          $turnsPrices[] = [
            'id'    => $turn['id'],
            'price' => $price['price']
          ];
        }  
      }
    }

    return $turnsPrices;
  }
}
