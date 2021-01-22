<?php

namespace App\Http\Modules\Adjustment;

use Illuminate\Validation\Rule;
use App\Http\Modules\Stock\StockMovement;
use Illuminate\Foundation\Http\FormRequest;

class AdjustmentRequest extends FormRequest
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
      'store_id'            => 'required|integer|store_visible',
      'description'         => 'required|string|max:255',
      'products'            => 'required|array',
      'products.*.quantity' => 'required|numeric|min:0',
      'products.*.id'       => ['required', 'distinct',
        Rule::exists('stock_stores', 'product_id')
          ->where('store_id', $this->get('store_id')),
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

    $validatedData['origin_type'] = StockMovement::OPTION_ORIGIN_TYPE_MANUAL_ADJUSTMENT;

    return $validatedData;
  }
  
}
