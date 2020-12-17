<?php

namespace App\Http\Modules\CashAdjustment;

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
            'store_id'    => 'required|integer|store_visible',
            'amount'      => 'required|numeric',
            'description' => 'required|string|max:255',
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

        $validatedData['type'] = CashAdjustment::OPTION_TYPE_MANUAL;

        return $validatedData;
    }

}
