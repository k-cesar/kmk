<?php

namespace App\Http\Modules\Store;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
      'address'                => 'required|string|max:500',
      'petty_cash_amount'      => 'required|numeric',
      'store_type_id'          => 'required|exists:store_types,id',
      'store_chain_id'         => 'required|exists:store_chains,id',
      'store_flag_id'          => 'required|exists:store_flags,id',
      'location_type_id'       => 'required|exists:location_types,id',
      'store_format_id'        => 'required|exists:store_formats,id',
      'company_id'             => 'required|exists:companies,id',
      'size'                   => 'required|numeric|min:0',
      'socioeconomic_level_id' => 'required|exists:socioeconomic_levels,id',
      'state_id'               => 'required|exists:states,id',
      'municipality_id'        => 'required|exists:municipalities,id',
      'zone_id'                => 'required|exists:zones,id',
      'latitute'               => 'required|between:-90,90',
      'longitude'              => 'required|between:-180,180',
      'name'                   => [
        'required', 
        'string',
        'max:150',
        Rule::unique('stores', 'name')
          ->where(function ($query) {
            return $query->where('zone_id', $this->get('zone_id'));
          }),
      ],
    ];

    if ($this->isMethod('PUT')) {
      $rules['name'] = [
        'required', 
        'string',
        'max:150',
        Rule::unique('stores', 'name')
          ->where(function ($query) {
            return $query->where('zone_id', $this->get('zone_id'))
              ->where('id', '!=', $this->store->id);
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

    if (auth()->user()->role->level > 1) {
      $validatedData['company_id'] = auth()->user()->company_id;
    }

    return $validatedData;
  }
  
}
