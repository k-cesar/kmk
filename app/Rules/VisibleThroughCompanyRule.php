<?php

namespace App\Rules;

use App\Traits\ResourceVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\Rule;

class VisibleThroughCompanyRule implements Rule
{
    /**
     * The table name to check.
     *
     * @var string
     */
    public $table;

    /**
     * Create a new rule instance.
     * 
     * @param  string  $table
     *
     * @return void
     */
    public function __construct($table=null)
    {
        $this->table = $table;
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
        $model = (new class($this->table) extends Model {
            use ResourceVisibility;

            public function __construct($table)
            {
                $this->table = $table;
            }
        });

        $resource = $model->where('id', $value)
            ->visibleThroughCompany(auth()->user())
            ->whereNull('deleted_at');

        return $resource->exists();
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
        $this->table = $params[0];

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
