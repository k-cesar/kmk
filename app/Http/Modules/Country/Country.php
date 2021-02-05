<?php

namespace App\Http\Modules\Country;

use App\Support\Helper;
use App\Traits\SecureDeletes;
use App\Http\Modules\Region\Region;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Currency\Currency;
use App\Http\Modules\Provider\Provider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\SocioeconomicLevel\SocioeconomicLevel;

class Country extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'currency_id',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['currency'];

    /**
     * Set the country's name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Helper::strToUpper($value);
    }

    /**
     * Get the currency that owns the country.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the companies for the country.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Get the providers for the country.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function providers()
    {
        return $this->hasMany(Provider::class);
    }

    /**
     * Get the regions for the country.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    /**
     * The socioeconomicLevel that belong to the country.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function socioeconomicLevel()
    {
        return $this->belongsToMany(SocioeconomicLevel::class, 'countries_socioeconomic');
    }

}
