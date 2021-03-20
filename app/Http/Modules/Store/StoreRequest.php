<?php

namespace App\Http\Modules\Store;

use App\Rules\IUniqueRule;
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
      'store_type_id'          => 'required|integer|exists:store_types,id,deleted_at,NULL',
      'store_chain_id'         => 'required|integer|exists:store_chains,id,deleted_at,NULL',
      'store_flag_id'          => 'required|integer|exists:store_flags,id,deleted_at,NULL',
      'location_type_id'       => 'required|integer|exists:location_types,id,deleted_at,NULL',
      'store_format_id'        => 'required|integer|exists:store_formats,id,deleted_at,NULL',
      'company_id'             => 'required|integer|exists:companies,id,deleted_at,NULL',
      'size'                   => 'required|numeric|min:0',
      'socioeconomic_level_id' => 'required|integer|exists:socioeconomic_levels,id,deleted_at,NULL',
      'state_id'               => 'required|integer|exists:states,id,deleted_at,NULL',
      'municipality_id'        => 'required|integer|exists:municipalities,id,deleted_at,NULL',
      'zone_id'                => 'required|integer|exists:zones,id,deleted_at,NULL',
      'latitute'               => 'required|numeric|between:-90,90',
      'longitude'              => 'required|numeric|between:-180,180',
      'name'                   => ['required', 'string', 'max:150',
        (new IUniqueRule('stores'))
          ->where('zone_id', $this->get('zone_id'))
          ->ignore($this->store),
      ],
    ];

    if ($this->isMethod('POST')) {
      $rules['petty_cash_amount'] = 'required|numeric|min:0';
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
