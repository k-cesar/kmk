<?php

namespace App\Http\Modules\Client;

use Illuminate\Support\Str;
use App\Traits\SecureDeletes;
use App\Http\Modules\Sell\Sell;
use App\Http\Modules\Country\Country;
use App\Http\Modules\Company\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes, SecureDeletes;

    const OPTION_TYPE_ADMIN   = 'ADMIN';
    const OPTION_TYPE_TENDERO = 'TENDERO';

    const OPTION_SEX_FEMALE = 'FEMALE';
    const OPTION_SEX_MALE   = 'MALE';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'country_id',
        'nit',
        'uuid',
        'address',
        'sex',
        'biometric_id',
        'birthdate',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            $client->uuid = Str::uuid()->toString();
        });
    }


    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['country'];


    /**
     * Returns all types options availables
     *
     * @return array
     */
    public static function getOptionsTypes()
    {
        return [
            self::OPTION_TYPE_ADMIN,
            self::OPTION_TYPE_TENDERO,
        ];
    }

    /**
     * Returns all sex options availables
     *
     * @return array
     */
    public static function getOptionsSex()
    {
        return [
            self::OPTION_SEX_FEMALE,
            self::OPTION_SEX_MALE,
        ];
    }

    /**
     * Get the country that owns the client.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the sells for the client.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function sells()
    {
        return $this->hasMany(Sell::class);
    }

    /**
     * The companies that belong to the client.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_clients')->withPivot('email', 'phone')->withTimestamps();
    }


    
}
