<?php

namespace App\Http\Modules\Store;


class StoreDuplicateRequest extends StoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules['store_id'] = 'required|integer|store_visible';

        return $rules;
    }
}
