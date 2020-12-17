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
            'store_id'               => ['required', 'integer', 'store_visible',
                function ($attribute, $value, $fail) {
                    $storeTurnOpen = StoreTurn::where('store_id', $value)
                        ->where('is_open', true)
                        ->exists();

                    if ($storeTurnOpen) {
                        $fail("La {$attribute} seleccionada ya posee un turno abierto.");
                    }
                }],
            'turn_id'                => ['required', 'integer',
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

            if (!$this->store_turn->is_open) {
                abort(404);
            }

            $rules = [
                'expenses_in_not_purchases' => 'required|numeric|min:0',
                'expenses_reason'           => 'present|nullable|string|max:255',
                'card_sales'                => 'required|numeric|min:0',
                'cash_on_hand'              => 'required|numeric|min:0',
            ];
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
            $validatedData['is_open']                = true;
            $validatedData['open_by']                = auth()->id();
            $validatedData['open_date']              = now();
            $validatedData['open_petty_cash_amount'] = Store::find($this->get('store_id'))->petty_cash_amount;
        } else {
            $validatedData['is_open']                  = false;
            $validatedData['closed_by']                = auth()->id();
            $validatedData['close_date']               = now();
            $validatedData['closed_petty_cash_amount'] = $this->store_turn->store->petty_cash_amount - $this->get('expenses_in_not_purchases');
        }

        return $validatedData;
    }
}
