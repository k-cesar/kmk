<?php

namespace App\Http\Modules\StoreTurn;

use App\Http\Modules\Turn\Turn;
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
            'store_id'               => 'required|integer|store_visible',
            'open_petty_cash_amount' => 'required|numeric|min:0',
            'turn_id'                => ['required','integer',
                function ($attribute, $value, $fail) {
                    $turn = Turn::where('id', $value)
                        ->where('store_id', $this->get('store_id', -1))
                        ->visibleThroughStore(auth()->user())
                        ->first();
                    
                    if (!$turn) {
                        $fail("El campo {$attribute} es inválido.");
                    }
                },
            ]
        ];

        if ($this->isMethod('PUT')) {
            unset($rules['open_petty_cash_amount']);

            $rules['closed_petty_cash_amount'] = 'required|numeric|min:0';
        }

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
            $validatedData['is_open'] = true;
            $validatedData['open_by'] = auth()->id();
            $validatedData['open_date'] = now();
        } else {
            $validatedData['is_open'] = false;
            $validatedData['closed_by'] = auth()->id();
            $validatedData['close_date'] = now();
        }

        return $validatedData;
    }
}
