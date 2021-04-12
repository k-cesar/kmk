<?php

namespace App\Http\Modules\StockCount;

use Illuminate\Foundation\Http\FormRequest;

class StockCountRequest extends FormRequest
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
            'store_id'                  => 'required|integer|store_visible',
            'products'                  => 'required|array',
            'products.*.quantity'       => 'required|numeric|min:0',
            'products.*.quantity_stock' => 'required|numeric|min:0',
            'products.*.id'             => "required|integer|distinct|exists:stock_stores,product_id,store_id,{$this->get('store_id')}",
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
        $validatedData['created_by'] = auth()->user()->id;
        $validatedData['count_date'] = now()->format('Y-m-d');
      }

      return $validatedData;
    }
}
