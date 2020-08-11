<?php

namespace App\Http\Modules\StockCountsDetail;

use Illuminate\Foundation\Http\FormRequest;

class StockCountsDetailRequest extends FormRequest
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
            'stock_count_id' => 'required|min:0|exists:stock_count,id',
            'product_id' => 'required|min:0|exists:products,id',
            'quantity' => 'required|min:0s',
        ];

        if($this->method('PUT')){
            $rules['stock_count_id'] = "required|min:0|exists:stock_count,id";
            $rules['product_id'] = "required|min:0|exists:products,id";
        }


        return $rules;
    }
}
