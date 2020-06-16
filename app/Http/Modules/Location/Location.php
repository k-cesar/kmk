<?php

namespace App\Http\Modules\Location;

use App\Http\Modules\Company\Company;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
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
        'company_id',
        'name',
        'active',
        'type',
        'municipalities_id',
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
     * Get the company that owns the location.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
