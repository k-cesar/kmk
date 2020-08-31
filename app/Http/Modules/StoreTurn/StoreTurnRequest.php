<?php

namespace App\Http\Modules\StoreTurn;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTurnRequest extends FormRequest
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
            'store_id'                  => 'required|exists:stores,id',
            'turn_id'                   => 'required|exists:turns,id',
            'open_petty_cash_amount'    => 'required|min:0',
            'open_by'                   => 'required|exists:users,id',
            'closed_by'                 => 'required|exists:users,id',
            'closed_petty_cash_amount'  => 'required|min:0',
            'open_date'                 => 'required|date|date_format:Y-m-d',
            'close_date'                 => 'required|date|date_format:Y-m-d',
            'is_open'                   => 'required|boolean',
        ];

        if ($this->isMethod('PUT')) {
            $rules['store_id'] = 'required|exists:stores,id';
            $rules['turn_id'] = 'required|exists:turns,id';
            $rules['open_by'] = 'required|exists:users,id';
            $rules['closed_by'] = 'required|exists:users,id';
            $rules['close_date'] = 'required|date|date_format:Y-m-d';
        }

        return $rules;
    }
}
