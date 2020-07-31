<?php

namespace App\Http\Modules\Turn;

use Illuminate\Validation\Rule;
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
      'store_id'   => 'required|exists:stores,id',
      'start_time' => 'required|date_format:H:i:s',
      'end_time'   => 'required|date_format:H:i:s|after:start_time',
      'is_active' =>  'required|integer|between:0,1',
      'is_default' => 'required|integer|between:0,1',
      'name'       => [
        'required', 
        'string',
        'max:255',
        Rule::unique('turns', 'name')
          ->where(function ($query) {
            return $query->where('store_id', $this->get('store_id'));
          }),
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
