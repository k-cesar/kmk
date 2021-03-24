<?php

namespace App\Http\Modules\StockCount;

use App\Traits\SecureDeletes;
use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockCount extends Model
{
    use SoftDeletes, SecureDeletes;

    const OPTION_STATUS_OPEN      = 'OPEN';
    const OPTION_STATUS_CLOSED    = 'CLOSED';
    const OPTION_STATUS_CANCELLED = 'CANCELLED';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id',
        'count_date',
        'status',
        'created_by',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'store',
        'stockCountDetails',
        'user',
    ];

    /**
    * Returns all types options availables
    *
    * @return array
    */
    public static function getOptionsStatus() {
        return [
            self::OPTION_STATUS_OPEN,
            self::OPTION_STATUS_CLOSED,
            self::OPTION_STATUS_CANCELLED,
        ];
    }

    /**
     * Get the store that owns the stockCount.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store() {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the user that owns the stockCount.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the stockCountDetails for the stockCount.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function stockCountDetails()
    {
        return $this->hasMany(StockCountDetail::class);
    }
}
