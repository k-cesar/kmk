<?php

namespace App\Rules;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Contracts\Validation\Rule;

class IUniqueRule extends Unique implements Rule 
{
    /**
     * The base query builder instance.
     *
     * @var \Illuminate\Database\Query\Builder
     */
    protected $query;

    /**
     * Create a new rule instance.
     *
     * @param  string  $table
     * @param  string  $column
     * @return void
     */
    public function __construct($table = null, $column = null)
    {
        $this->table  = $table;

        $this->column = $column;
        
        $this->query  = DB::table($this->table);
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->column = $this->column ?: $attribute;

        $this->value  = $value;

        return $this->buildQuery()->count() == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El valor del campo :attribute ya está en uso.';
    }

    /**
     * Build the quiery to find matches
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function buildQuery()
    {
        return $this->query
            ->whereRaw("LOWER({$this->column}) = LOWER(?)", $this->value)
            ->when($this->ignore, function ($query) {
                $query->where($this->idColumn, '!=', $this->ignore);
            });
    }

    /**
     * Validate the uniqueness of an attribute value on a given database table.
     *
     * If a database column is not specified, the attribute will be used.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    public function validate($attribute, $value, $params)
    {
        $this->table  = $params[0];
        $this->column = $params[1] ?? $attribute;

        $this->ignore   = $params[2] ?? null;
        $this->idColumn = $params[3] ?? $this->idColumn;

        $this->query = DB::table($this->table);
        
        $this->addExtraWheres($params);

        return $this->passes($attribute, $value);
    }

    /**
     * Add extra conditions for a iunique rule.
     *
     * @param array $params
     * @return $this
     */
    protected function addExtraWheres($params)
    {
        if (isset($params[4])) {
            $extraParams = array_slice($params, 4);

            $count = count($extraParams);

            for ($i = 0; $i < $count; $i += 2) {
                $this->query->where($extraParams[$i], $extraParams[$i + 1]);
            }
        }

        return $this->query;
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param  \Closure|string|array  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->query->where($column, $operator, $value, $boolean);

        return $this;
    }

    /**
     * Add a "where in" clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $values
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $this->query->whereIn($column, $values, $boolean, $not);

        return $this;
    }
}
