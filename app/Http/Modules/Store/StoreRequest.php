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
      'store_type_id'          => 'required|integer|exists:store_types,id',
      'store_chain_id'         => 'required|integer|exists:store_chains,id',
      'store_flag_id'          => 'required|integer|exists:store_flags,id',
      'location_type_id'       => 'required|integer|exists:location_types,id',
      'store_format_id'        => 'required|integer|exists:store_formats,id',
      'company_id'             => 'required|integer|exists:companies,id',
      'size'                   => 'required|numeric|min:0',
      'socioeconomic_level_id' => 'required|integer|exists:socioeconomic_levels,id',
      'state_id'               => 'required|integer|exists:states,id',
      'municipality_id'        => 'required|integer|exists:municipalities,id',
      'zone_id'                => 'required|integer|exists:zones,id',
      'latitute'               => 'required|numeric|between:-90,90',
      'longitude'              => 'required|numeric|between:-180,180',
      'name'                   => ['required', 'string', 'max:150',
        Rule::unique('stores', 'name')
          ->where('zone_id', $this->get('zone_id')),
      ],
    ];

    if ($this->isMethod('POST')) {
      $rules['petty_cash_amount'] = 'required|numeric|min:0';
    } else {
      $rules['name'] = ['required', 'string', 'max:150',
        Rule::unique('stores', 'name')
          ->where('zone_id', $this->get('zone_id'))
          ->whereNot('id', $this->store->id),
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
