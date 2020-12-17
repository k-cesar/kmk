<?php

namespace App\Http\Modules\Purchase;

use App\Traits\SecureDeletes;
use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Provider\Provider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\PaymentMethod\PaymentMethod;

class Purchase extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id',
        'user_id',
        'comments',
        'invoice',
        'serial_number',
        'date',
        'total',
        'provider_id',
        'payment_method_id',
    ];

    /**
     * Get the store that owns the purchase.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the user that owns the purchase.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the provider that owns the purchase.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the paymentMethod that owns the purchase.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the purchaseDetails for the purchase.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }


}
