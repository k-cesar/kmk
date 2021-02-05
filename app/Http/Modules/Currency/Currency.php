<?php

namespace App\Http\Modules\Currency;

use App\Traits\SecureDeletes;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Country\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'symbol',
        'abbreviation',
        'description',
        'disabled',
    ];

    /**
     * Set the currency's name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value)), 'utf-8');
    }

    /**
     * Set the currency's abbreviation.
     *
     * @param  string  $value
     * @return void
     */
    public function setAbbreviationAttribute($value)
    {
        $this->attributes['abbreviation'] = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value)), 'utf-8');
    }

    /**
     * Get the countries for the currency.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function countries()
    {
        return $this->hasMany(Country::class);
    }

    /**
     * Get the companies for the currency.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

}
