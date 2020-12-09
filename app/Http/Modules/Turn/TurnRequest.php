<?php

namespace App\Http\Modules\Turn;

use Illuminate\Validation\Rule;
use App\Http\Modules\Store\Store;
use Illuminate\Foundation\Http\FormRequest;

class TurnRequest extends FormRequest
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
      'start_time' => 'required|date_format:H:i:s',
      'end_time'   => 'required|date_format:H:i:s|after:start_time',
      'is_active' =>  'required|boolean',
      'is_default' => 'required|boolean',
      'name'       => [
        'required', 
        'string',
        'max:255',
        Rule::unique('turns', 'name')
          ->where(function ($query) {
            return $query->where('store_id', $this->get('store_id'));
          }),
      ],
      'store_id' => [
        'required',
        'integer',
        function ($attribute, $value, $fail) {
          $store = Store::where('id', $value)
            ->visible(auth()->user())
            ->first();

          if (!$store) {
            $fail("El campo {$attribute} es inválido.");
          }
        },
      ],
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = [
        'required', 
        'string',
        'max:255',
        Rule::unique('turns', 'name')
          ->where(function ($query) {
            return $query->where('store_id', $this->get('store_id'))
              ->where('id', '!=', $this->turn->id);
          }),
      ];
    }

    return $rules;
  }
  
}
