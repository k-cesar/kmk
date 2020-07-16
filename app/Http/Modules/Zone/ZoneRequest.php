<?php

namespace App\Http\Modules\Zone;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ZoneRequest extends FormRequest
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
      'municipality_id' => 'required|exists:municipalities,id',
      'name'     => [
        'required', 
        'string',
        'max:150',
        Rule::unique('zones', 'name')
          ->where(function ($query) {
            return $query->where('municipality_id', $this->get('municipality_id'));
          }),
      ],
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = [
        'required', 
        'string',
        'max:150',
        Rule::unique('zones', 'name')
          ->where(function ($query) {
            return $query->where('municipality_id', $this->get('municipality_id'))
              ->where('id', '!=', $this->zone->id);
          }),
      ];
    }

    return $rules;
  }
  
}
