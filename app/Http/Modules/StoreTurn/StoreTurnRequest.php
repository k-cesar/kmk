<?php

namespace App\Http\Modules\StoreTurn;

use App\Http\Modules\Turn\Turn;
use App\Http\Modules\Store\Store;
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
            'open_petty_cash_amount'    => 'required|numeric|min:0',
		    'store_id' => [
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
            'turn_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $turn = Turn::where('id', $value)
                        ->where('store_id', $this->get('store_id', -1))
                        ->visible(auth()->user())
                        ->first();

                    if (!$turn) {
                        $fail("El campo {$attribute} es inválido.");
                    }
                },
            ]
        ];

        if ($this->isMethod('PUT')) {
            unset($rules['open_petty_cash_amount']);
        }

        return $rules;
    }
}
