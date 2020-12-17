<?php

namespace App\Http\Modules\StoreTurn;

use App\Traits\SecureDeletes;
use App\Http\Modules\Sell\Sell;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\Store\Store;
use App\Traits\ResourceVisibility;
use App\Http\Modules\Deposit\Deposit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\SellPayment\SellPayment;

class StoreTurn extends Model
{
    use SoftDeletes, SecureDeletes, ResourceVisibility;

    protected $fillable = [
        'store_id',
        'turn_id',
        'is_open',
        'open_by',
        'open_date',
        'open_petty_cash_amount',
        'closed_by',
        'close_date',
        'closed_petty_cash_amount',
        'expenses_in_not_purchases',
        'expenses_reason',
        'card_sales',
        'cash_on_hand',
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

    /**
     * Get the turn that owns the StoreTurn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function turn()
    {
        return $this->belongsTo(Turn::class);
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

    /**
     * Get the sells for the storeTurn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function sellPayments()
    {
        return $this->hasMany(SellPayment::class);
    }

}
