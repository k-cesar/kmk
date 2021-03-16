<?php

namespace App\Http\Modules\PresentationSku;

use App\Rules\IUniqueRule;
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
      'description'      => 'required|string|max:255',
      'presentation_id'  => 'required|integer|visible_through_company:presentations',
      'seasonal_product' => 'required|boolean',
      'code'             => ['required', 'alpha_num', 'max:150',
        (new IUniqueRule('presentation_skus'))
          ->whereIn('company_id', [0, auth()->user()->company_id])
          ->ignore($this->presentation_sku),
      ],
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

    if ($this->isMethod('POST')) {
      $validatedData['company_id'] = auth()->user()->company_id;
    }

    return $validatedData;
  }
  
}
