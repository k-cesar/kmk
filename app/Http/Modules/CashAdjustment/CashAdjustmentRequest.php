<?php

namespace App\Http\Modules\CashAdjustment;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Modules\CashAdjustment\CashAdjustment;

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
            'store_id'          => 'required|integer|min:0|exists:stores,id',
            'store_turn_id'      => [
                'required',
                Rule::exists('store_turns', 'id')
                ->where(function ($query) {
                    return $query->where('is_open', 1);
                }),
            ],
            'amount'            => 'required|numeric',
            'modification_type' => 'required|in:'.implode(',', CashAdjustment::getModificationTypes()),
            'description'       => 'required|string|max:255'
        ];

        if($this->isMethod('PUT')) {
            $rules['store_id'] = 'required|integer|min:0|exists:stores,id';
            $rules['store_turn_id'] = [
                'required',
                Rule::exists('store_turns', 'id')
                ->where(function ($query) {
                    return $query->where('is_open', 1);
                }),
            ];
            $rules['modification_type'] = 'required|in:'.implode(',', CashAdjustment::getModificationTypes());
        }

        return $rules;
    }
}
