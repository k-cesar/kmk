<?php

namespace App\Http\Modules\Company;

use App\Http\Modules\Currency\Currency;
use App\Http\Modules\Location\Location;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use SecureDeletes;

    const ACTIVE_OPTION_Y = 'Y';
    const ACTIVE_OPTION_N = 'N';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nit',
        'name',
        'comercial_name',
        'comercial_address',
        'active',
        'currency_id',
    ];

    /**
     * Returns all active options available
     *
     * @return array
     */
    public  static function getActiveOptions()
    {
        return [
            self::ACTIVE_OPTION_Y,
            self::ACTIVE_OPTION_N
        ];
    }

    /**
     * Get the currency that owns the company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the locations for the company.
     * 
     * @return @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companies()
    {
        return $this->hasMany(Location::class);
    }
}
