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

    const OPTION_STATUS_OPEN   = 'OPEN';
    const OPTION_STATUS_CLOSED = 'CLOSED';
    const OPTION_STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'store_id',
        'count_date',
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
        return $this->hasMany(StockCountDetail::class, 'stock_count_id');
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
