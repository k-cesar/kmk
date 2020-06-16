<?php

namespace App\Http\Modules\Company;

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
}
