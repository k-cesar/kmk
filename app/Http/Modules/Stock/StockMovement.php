<?php

namespace App\Http\Modules\Stock;

use App\Traits\SecureDeletes;
use App\Http\Modules\Store\Store;
use App\Http\Modules\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use SoftDeletes, SecureDeletes;

    const OPTION_ORIGIN_TYPE_MANUAL_ADJUSTMENT = 'MANUAL_ADJUSTMENT';
    const OPTION_ORIGIN_TYPE_PURCHASE          = 'PURCHASE';
    const OPTION_ORIGIN_TYPE_SELL              = 'SELL';
    const OPTION_ORIGIN_TYPE_TRANSFER          = 'TRANSFER';

    const OPTION_MOVEMENT_TYPE_ADJUSTMENT = 'ADJUSTMENT';
    const OPTION_MOVEMENT_TYPE_INPUT      = 'INPUT';
    const OPTION_MOVEMENT_TYPE_OUTPUT     = 'OUTPUT';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'decription',
        'user_id',
        'origin_type',
        'origin_id',
        'date',
        'movement_type',
        'store_id',
    ];

    /**
     * Returns all origin types options availables
     *
     * @return array
     */
    public static function getOptionsOriginTypes()
    {
        return [
           self::OPTION_ORIGIN_TYPE_MANUAL_ADJUSTMENT,
           self::OPTION_ORIGIN_TYPE_PURCHASE,
           self::OPTION_ORIGIN_TYPE_SELL,
           self::OPTION_ORIGIN_TYPE_TRANSFER,
        ];
    }

    /**
     * Returns all movement types options availables
     *
     * @return array
     */
    public static function getOptionsMovementTypes()
    {
        return [
           self::OPTION_MOVEMENT_TYPE_ADJUSTMENT,
           self::OPTION_MOVEMENT_TYPE_INPUT,
           self::OPTION_MOVEMENT_TYPE_OUTPUT,
        ];
    }


    /**
     * Get the store that owns the stock movement.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the user that owns the stock movement.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the stockMovementDetails for the stockMovement.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function stockMovementDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

}
