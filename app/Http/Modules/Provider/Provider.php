<?php

namespace App\Http\Modules\Provider;

use Illuminate\Support\Str;
use App\Traits\SecureDeletes;
use App\Http\Modules\Country\Country;
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
     * Get the country that owns the brand.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

}
