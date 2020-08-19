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
      'provider_id'       => 'required|exists:providers,id',
      'store_id'          => 'required|exists:stores,id',
      'payment_method_id' => 'required|exists:payment_methods,id',
      'invoice'           => 'required|string|max:100',
      'comments'          => 'sometimes|nullable|string|max:250'
    ];

    if ($this->isMethod('POST')) {
      $rules = array_merge($rules, [
        'products'              => 'required|array',
        'products.*.id'         => 'required|exists:products,id',
        'products.*.quantity'   => 'required|numeric|min:0',
        'products.*.unit_price' => 'required|numeric|min:0',
      ]);
    }

    return $rules;
  }
  
}
