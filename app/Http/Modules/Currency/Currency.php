<?php

namespace App\Http\Modules\Currency;

use App\Support\Helper;
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
        $this->attributes['name'] = Helper::strToUpper($value);
    }

    /**
     * Set the currency's abbreviation.
     *
     * @param  string  $value
     * @return void
     */
    public function setAbbreviationAttribute($value)
    {
        $this->attributes['abbreviation'] = Helper::strToUpper($value);
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
