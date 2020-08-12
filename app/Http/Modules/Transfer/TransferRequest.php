<?php

namespace App\Http\Modules\Transfer;

use App\Http\Modules\Store\Store;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class TransferRequest extends FormRequest
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
      'origin_store_id'     => 'required|exists:stores,id',
      'destiny_store_id'    => 'required|exists:stores,id|different:origin_store_id',
      'products'            => 'required|array',
      'products.*.id'       => [
        'required',
        Rule::exists('stock_stores', 'product_id')
          ->where(function ($query) {
            return $query->where('store_id', $this->get('origin_store_id'));
          }),
      ],
      'products.*.quantity' => [
        'required',
        'numeric',
        'min:0',
        function ($attribute, $value, $fail) {
          $stock = DB::table('stock_stores')
            ->where('store_id', $this->get('origin_store_id'))
            ->where('product_id', $this->get('origin_store_id'))
            ->first();

          $stockQuantity = $stock ? $stock->quantity : 0;

          if ($value > $stockQuantity) {
            $fail("La cantidad a ingresada({$value}) supera a la cantidad en stock({$stockQuantity})");
          }
        },
      ],
    ];

    return $rules;
  }
  
}
