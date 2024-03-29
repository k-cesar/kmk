<?php

namespace App\Http\Modules\Client;

use App\Support\Helper;
use Illuminate\Support\Str;
use App\Traits\SecureDeletes;
use App\Http\Modules\Sell\Sell;
use App\Http\Modules\User\User;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Country\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes, SecureDeletes;

    const OPTION_TYPE_INDIVIDUAL   = 'INDIVIDUAL';
    const OPTION_TYPE_CORPORATION  = 'CORPORATION';

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
            self::OPTION_TYPE_INDIVIDUAL,
            self::OPTION_TYPE_CORPORATION,
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
     * Set the client's nit.
     *
     * @param  string  $value
     * @return void
     */
    public function setNitAttribute($value)
    {
        $this->attributes['nit'] = Helper::strToUpper($value);
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
        return $this->belongsToMany(Company::class, 'company_clients')->withPivot('email', 'phone')->withTimestamps()->withTrashed();
    }

    /**
     * Scope a query to only include clients visible by the user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Http\Modules\User\User $user
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, User $user)
    {
        if ($user->role->level > 1) {
            return $query->where('country_id', $user->company->country_id);
        }

        return $query;
    }


    
}
