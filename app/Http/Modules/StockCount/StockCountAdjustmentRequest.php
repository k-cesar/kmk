<?php

namespace App\Http\Modules\StockCount;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StockCountAdjustmentRequest extends FormRequest
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
      'store_id'       => 'required|integer|store_visible',
      'stock_count_id' => [
        'required',
        Rule::exists('stock_counts', 'id')
          ->where(function ($query) {
            return $query->where('store_id', $this->get('store_id'))
              ->where('status', StockCount::OPTION_STATUS_CLOSED);
          }),
      ],
    ];

    return $rules;
  }

}
