<?php

namespace App\Http\Modules\Transfer;

use App\Http\Modules\Presentation\Presentation;
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
      'origin_store_id'    => 'required|exists:stores,id',
      'destiny_store_id'   => 'required|exists:stores,id|different:origin_store_id',
      'presentations'      => 'required|array',
      'presentations.*.id' => 'required|distinct|exists:presentations,id',
    ];

    $presentations = Presentation::whereIn('id', collect($this->get('presentations'))->pluck('id'))
      ->with('product')
      ->get();

    $stock = DB::table('stock_stores')
      ->where('store_id', $this->get('origin_store_id'))
      ->whereIn('product_id', $presentations->pluck('product_id')->unique())
      ->get();

    foreach($presentations as $index => $presentation) {
      $rules["presentations.$index.quantity"] = [
        'required',
        'integer',
        'min:1',
        function ($attribute, $value, $fail) use ($presentation, $stock) {
          $productInStock = $stock->where('product_id', $presentation->product_id)->first();

          $stockQuantity = $productInStock ? $productInStock->quantity : 0;
          $transferQuantity = $value * $presentation->units;

          if ($transferQuantity > $stockQuantity) {
            $fail("La cantidad a transferir ({$transferQuantity}) supera a la cantidad restante en stock ({$stockQuantity})");
          } else {
            $productInStock->quantity -= $value * $presentation->units;

            $stock[$stock->search($productInStock)] = $productInStock;
          }
        },
      ];
    }

    return $rules;
  }
  
}
