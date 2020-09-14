<?php

namespace App\Http\Modules\StoreTurn;

use App\Traits\SecureDeletes;
use App\Http\Modules\Sell\Sell;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Deposit\Deposit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\CashAdjustment\CashAdjustment;
use App\Http\Modules\StoreTurnModification\StoreTurnModification;

class StoreTurn extends Model
{
    use SoftDeletes, SecureDeletes;

    protected $fillable = [
        'store_id',
        'turn_id',
        'open_petty_cash_amount',
        'open_by',
        'open_date',
        'is_open',
    ];

    protected $with = [
        'turn_modification',
    ];

    /**
     * Get the sells for the storeTurn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function sells()
    {
        return $this->hasMany(Sell::class);
    }

    /**
     * Get the store that owns the StoreTurn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function turn_modification()
    {
        return $this->hasMany(StoreTurnModification::class, 'store_turn_id');
    }

    /**
     * Get the deposits for the storeTurn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }
}
