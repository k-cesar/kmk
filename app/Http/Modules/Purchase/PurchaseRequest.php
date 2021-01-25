<?php

namespace App\Http\Modules\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
      'provider_id'   => 'required|integer|exists:providers,id,deleted_at,NULL',
      'invoice'       => 'required|string|max:100',
      'serial_number' => 'sometimes|nullable|string|max:100',
      'comments'      => 'sometimes|nullable|string|max:250',
    ];

    if ($this->isMethod('POST')) {
      $rules = array_merge($rules, [
        'store_id'                   => 'required|integer|store_visible',
        'payment_method_id'          => 'required|integer|visible_through_company:payment_methods',
        'presentations'              => 'required|array',
        'presentations.*.id'         => 'required|integer|distinct|visible_through_company:presentations',
        'presentations.*.quantity'   => 'required|numeric|min:0',
        'presentations.*.unit_price' => 'required|numeric|min:0',
      ]);
    }

    return $rules;
  }
  
}
