<?php

namespace App\Http\Modules\CashAdjustment;

use Illuminate\Validation\Rule;
use App\Http\Modules\Store\Store;
use Illuminate\Foundation\Http\FormRequest;

class CashAdjustmentRequest extends FormRequest
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
            'amount'      => 'required|numeric',
            'description' => 'required|string|max:255',
            'store_id'    => [
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
            'store_turn_id' => [
                'required',
                Rule::exists('store_turns', 'id')
                ->where(function ($query) {
                    return $query->where('is_open', 1);
                }),
            ],
        ];

        return $rules;
    }
}
