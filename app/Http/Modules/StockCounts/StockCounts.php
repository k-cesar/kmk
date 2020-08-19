<?php

namespace App\Http\Modules\StockCounts;

use App\Traits\SecureDeletes;
use App\Http\Modules\Store\Store;
use App\Http\Modules\User\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Http\Modules\StockCountsDetail\StockCountsDetail;

class StockCounts extends Model
{
    use SoftDeletes, SecureDeletes;

    const OPTION_STATUS_OPEN   = 'OPEN';
    const OPTION_STATUS_CLOSED = 'CLOSED';
    const OPTION_STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'count_date',
        'store_id',
        'status',
        'created_by',
    ];

    protected $with = [
        'store',
        'stock_counts',
        'user',
    ];

    public function store() {
        return $this->belongsTo(Store::class);
    }

    public function stock_counts() { 
        return $this->hasMany(StockCountsDetail::class, 'stock_count_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getOptionsStatus() {
        return [
            self::OPTION_STATUS_OPEN,
            self::OPTION_STATUS_CLOSED,
            self::OPTION_STATUS_CANCELLED,
        ];
    }

    public static function getOptionStatusForUpdate() {
        return [
            self::OPTION_STATUS_OPEN,
        ];
    }
}
