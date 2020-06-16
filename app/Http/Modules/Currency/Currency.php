<?php

namespace App\Http\Modules\Currency;

use App\Http\Modules\Company\Company;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use SecureDeletes;

    const ACTIVE_OPTION_Y = 'Y';
    const ACTIVE_OPTION_N = 'N';

    const MAIN_CURRENCY_OPTION_Y = 'Y';
    const MAIN_CURRENCY_OPTION_N = 'N';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'symbol',
        'description',
        'active',
        'main_currency',
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
     * Returns all main currency options available
     *
     * @return array
     */
    public  static function getMainCurrencyOptions()
    {
        return [
            self::MAIN_CURRENCY_OPTION_Y,
            self::MAIN_CURRENCY_OPTION_N
        ];
    }

    /**
     * Get the companies for the currency.
     * 
     * @return @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
