<?php

namespace App\Http\Modules\Currency;

use App\Http\Modules\Company\Company;
use App\Http\Modules\Country\Country;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use SecureDeletes;

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
