<?php

namespace App\Http\Modules\Provider;

use App\Support\Helper;
use Illuminate\Support\Str;
use App\Traits\SecureDeletes;
use App\Http\Modules\Country\Country;
use App\Http\Modules\Purchase\Purchase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nit',
        'country_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($provider) {
            $provider->uuid = Str::uuid()->toString();
        });
    }

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['country'];

    /**
     * Set the provider's nit.
     *
     * @param  string  $value
     * @return void
     */
    public function setNitAttribute($value)
    {
        $this->attributes['nit'] = Helper::strToUpper($value);
    }

    /**
     * Get the country that owns the brand.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the purchases for the provider.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

}
