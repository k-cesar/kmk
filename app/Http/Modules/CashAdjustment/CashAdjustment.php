<?php

namespace App\Http\Modules\CashAdjustment;

use App\Traits\SecureDeletes;
use App\Http\Modules\Store\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashAdjustment extends Model
{
    use SoftDeletes, SecureDeletes;

    const OPTION_TYPE_MANUAL = 'MANUAL';

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'store_id',
        'amount',
        'type',
        'description',
    ];

    /**
    * Returns all types options availables
    *
    * @return array
    */
    public static function getOptionsTypes()
    {
        return [
            self::OPTION_TYPE_MANUAL,
        ];
    }

    /**
     * Get the store that owns the cashAdjustment.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

}
