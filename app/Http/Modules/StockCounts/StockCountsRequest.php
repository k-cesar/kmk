<?php

namespace App\Http\Modules\StockCounts;

use App\Http\Modules\Store\Store;
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
            'status'     => 'required|in:'.implode(',', StockCounts::getOptionsStatus()),
            'created_by' => 'required|exists:users,id',
            'store_id'   => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $store = Store::where('id', $value)
                        ->visible(auth()->user())
                        ->first();

                    if (!$store) {
                        $fail("El campo {$attribute} es inválido.");
                    }
                },
            ],
        ];

        if($this->isMethod('PUT')) {
            $rules['status'] = 'required|in:'.implode(',', StockCounts::getOptionStatusForUpdate());
        }

        return $rules;
    }
}
