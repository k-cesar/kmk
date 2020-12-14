<?php

namespace App\Http\Modules\StoreTurnModification;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTurnModificationRequest extends FormRequest
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
            'amount'      => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'store_turn_id' => [
                'required',
                Rule::exists('store_turns', 'id')
                ->where(function ($query) {
                    return $query->where('is_open', 1)
                        ->where('store_id', $this->get('store_id'));
                }),
            ],
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

        $validatedData['modification_type'] = StoreTurnModification::OPTION_MODIFICATION_TYPE_CASH_PURCHASE;

        return $validatedData;
    }

}
