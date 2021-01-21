<?php

namespace App\Http\Modules\StoreTurn;

use App\Traits\SecureDeletes;
use App\Http\Modules\Sell\Sell;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\Store\Store;
use App\Traits\ResourceVisibility;
use App\Http\Modules\Deposit\Deposit;
use App\Http\Modules\Purchase\Purchase;
use Illuminate\Database\Eloquent\Model;
use App\Http\Modules\SellPayment\SellPayment;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\PaymentMethod\PaymentMethod;
use App\Http\Modules\CashAdjustment\CashAdjustment;

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

    /**
     * Load the card_sales_pos, expenses_in_purchases, deposits_total, 
     * cash_sales, cash_collected_in_receivables 
     * and cash_adjustments_total of the StoreTurn
     *
     * @return App\Http\Modules\StoreTurn\StoreTurn
     */
    public function loadAmounts()
    {
        $this->card_sales_pos = $this->sellPayments()
            ->where('status', SellPayment::OPTION_STATUS_VERIFIED)
            ->where('payment_method_id', PaymentMethod::where('name', PaymentMethod::OPTION_PAYMENT_CARD)->first()->id)
            ->pluck('amount')
            ->sum();

        $this->expenses_in_purchases = Purchase::where('store_id', $this->store_id)
            ->where('payment_method_id', PaymentMethod::where('name', PaymentMethod::OPTION_PAYMENT_CASH)->first()->id)
            ->whereBetween('date', [$this->open_date, $this->close_date])
            ->pluck('total')
            ->sum();

        $this->deposits_total = $this->deposits
            ->pluck('amount')
            ->sum();

        $this->cash_sales = $this->sellPayments()
            ->where('status', SellPayment::OPTION_STATUS_VERIFIED)
            ->where('payment_method_id', PaymentMethod::where('name', PaymentMethod::OPTION_PAYMENT_CASH)->first()->id)
            ->whereHas('sell', function ($query) {
                $query->where('status', Sell::OPTION_STATUS_PAID)
                    ->where('is_to_collect', false);
            })
            ->pluck('amount')
            ->sum();

        $this->cash_collected_in_receivables = $this->sellPayments()
            ->where('status', SellPayment::OPTION_STATUS_VERIFIED)
            ->where('payment_method_id', PaymentMethod::where('name', PaymentMethod::OPTION_PAYMENT_CASH)->first()->id)
            ->whereHas('sell', function ($query) {
                $query->where('status', Sell::OPTION_STATUS_PAID)
                    ->where('is_to_collect', true);
            })
            ->pluck('amount')
            ->sum();

        $this->cash_adjustments_total = CashAdjustment::where('store_id', $this->store_id)
            ->whereBetween('created_at', [$this->open_date, $this->close_date])
            ->pluck('amount')
            ->sum();

        return $this;
    }

}
