<?php

namespace App\Http\Modules\SellPayment;

use App\Http\Modules\Sell\Sell;
use Illuminate\Database\Eloquent\Model;
use App\Http\Modules\StoreTurn\StoreTurn;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\PaymentMethod\PaymentMethod;

class SellPayment extends Model
{
    use SoftDeletes;
    
    const OPTION_STATUS_VERIFIED   = 'VERIFIED';
    const OPTION_STATUS_UNVERIFIED = 'UNVERIFIED';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sell_id',
        'payment_method_id',
        'store_turn_id',
        'amount',
        'card_four_digits',
        'authorization',
        'status',
    ];

    /**
     * Get the sell that owns the sell payment.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sell()
    {
        return $this->belongsTo(Sell::class);
    }

    /**
     * Get the paymentMethod that owns the sell payment.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the storeTurn that owns the sellPayment.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storeTurn()
    {
        return $this->belongsTo(StoreTurn::class);
    }

}
