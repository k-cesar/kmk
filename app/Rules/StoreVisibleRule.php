<?php

namespace App\Rules;

use App\Http\Modules\Store\Store;
use Illuminate\Contracts\Validation\Rule;

class StoreVisibleRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $
     * 
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $store = Store::where('id', $value)
            ->visible(auth()->user());

        return $store->exists();
    }

    /**
    * Validate the rule.
    *
    * @param  string  $attribute
    * @param  string  $attribute
    * @param  mixed  $params
    * 
    * @return bool
    */
    public function validate($attribute, $value, $params)
    {
        return $this->passes($attribute, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El campo :attribute seleccionado es inválido.';
    }
}
