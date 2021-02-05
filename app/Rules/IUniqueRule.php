<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

class IUniqueRule extends Unique implements Rule 
{
    use ValidatesAttributes;

    /**
     * Create a new rule instance.
     *
     * @param  string  $table
     * @param  string  $column
     * @return void
     */
    public function __construct($table = '', $column = 'NULL')
    {
        $this->column = $column;

        $this->table = $this->resolveTableName($table);
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
        $table       = $this->table;
        $column      = $this->column=='NULL' ? $attribute : $this->column;
        $id          = $this->ignore;
        $idColumn    = $this->idColumn;
        $extra       = array_merge(
            $this->extra ?? [],
            $this->formatWheres()->toArray(),
            $this->queryCallbacks()
        );

        return $this->getCount(
            $table, $column, $value, $id, $idColumn, $extra
        ) == 0;
    }

    /**
     * Validate the uniqueness of an attribute value on a given database table.
     *
     * If a database column is not specified, the attribute will be used.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     * @return bool
     */
    public function validate($attribute, $value, $parameters)
    {
        $this->table = $parameters[0];

        $this->column = $parameters[1] ?? $attribute;

        $this->idColumn = $this->ignore = null;

        if (isset($parameters[2])) {
            [$this->idColumn, $this->ignore] = $this->getUniqueIds($parameters);

            if (! is_null($this->ignore)) {
                $this->ignore = stripslashes($this->ignore);
            }
        }

        $this->extra = $this->getUniqueExtra($parameters);

        return $this->passes($attribute, $value);
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
     * Count the number of objects in a table having the given value.
     *
     * @param  string  $table
     * @param  string  $column
     * @param  string  $value
     * @param  int|null  $excludeId
     * @param  string|null  $idColumn
     * @param  array  $extra
     * @return int
     */
    public function getCount($table, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        $query = DB::table($table)->whereRaw("LOWER($column) = LOWER(?)", [$value]);

        if (! is_null($excludeId) && $excludeId !== 'NULL') {
            $query->where($idColumn ?: 'id', '<>', $excludeId);
        }

        return $this->addConditions($query, $extra)->count();
    }

    /**
     * Add the given conditions to the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $conditions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function addConditions($query, $conditions)
    {
        foreach ($conditions as $key => $value) {
            if ($value instanceof Closure) {
                $query->where(function ($query) use ($value) {
                    $value($query);
                });
            } else {
                $this->addWhere($query, $key, $value);
            }
        }

        return $query;
    }

    /**
     * Add a "where" clause to the given query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $key
     * @param  string  $extraValue
     * @return void
     */
    protected function addWhere($query, $key, $extraValue)
    {
        if ($extraValue === 'NULL') {
            $query->whereNull($key);
        } elseif ($extraValue === 'NOT_NULL') {
            $query->whereNotNull($key);
        } elseif (Str::startsWith($extraValue, '!')) {
            $query->where($key, '!=', mb_substr($extraValue, 1));
        } else {
            $query->where($key, $extraValue);
        }
    }

    /**
     * Format the where clauses.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function formatWheres()
    {
        return collect($this->wheres)->map(function ($where) {
            return [$where['column'] => $where['value']];
        })->collapse();
    }
}
