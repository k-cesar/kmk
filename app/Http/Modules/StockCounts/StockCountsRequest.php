<?php

namespace App\Http\Modules\StockCounts;

use Illuminate\Foundation\Http\FormRequest;

class StockCountsRequest extends FormRequest
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
            'count_date' => 'required|date|date_format:Y-m-d',
            'store_id' => 'required|exists:stores,id',
            'status' => 'required|string|max:100',
            'created_by' => 'required|exists:users,id',
        ];

        if($this->method('PUT')) {
            $rules['store_id'] = "required|exists:stores,id";
            $rules['created_by'] = "required|exists:users,id";
        }

        return $rules;
    }
}
