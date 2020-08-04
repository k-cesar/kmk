<?php

namespace App\Http\Modules\PresentationSku;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PresentationSkuRequest extends FormRequest
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
      'code'             => 'required|alpha_num|max:150|unique:presentation_skus',
      'description'      => 'required|string|max:255',
      'presentation_id'  => 'required|exists:presentations,id',
      'seasonal_product' => 'required|boolean',
    ];

    if ($this->isMethod('PUT')) {
      $rules['code'] = "required|string|max:150|unique:presentation_skus,code,\"{$this->presentation_sku->code}\",code";
    }

    return $rules;
  }
  
}
