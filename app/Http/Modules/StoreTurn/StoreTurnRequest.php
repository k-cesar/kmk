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
        ];



        if ($this->isMethod('PUT')) {
            unset($rules['open_petty_cash_amount']);
        }

        return $rules;
    }
}
