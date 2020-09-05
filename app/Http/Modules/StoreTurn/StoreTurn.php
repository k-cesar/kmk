<?php

namespace App\Http\Modules\StoreTurn;

use App\Traits\SecureDeletes;
use App\Http\Modules\Store\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\StoreTurnModification\StoreTurnModification;

class StoreTurn extends Model
{
    use SoftDeletes, SecureDeletes;

    protected $fillable = [
        'store_id',
        'turn_id',
        'open_petty_cash_amount',
        'open_by',
        'closed_by',
        'closed_petty_cash_amount',
        'open_date',
        'close_date',
        'is_open',
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
}
