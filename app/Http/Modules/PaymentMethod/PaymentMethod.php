<?php

namespace App\Http\Modules\PaymentMethod;

use App\Traits\SecureDeletes;
use App\Traits\ResourceVisibility;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Purchase\Purchase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes, SecureDeletes, ResourceVisibility;

    const OPTION_PAYMENT_CASH   = 'CASH';
    const OPTION_PAYMENT_CARD   = 'CARD';
    const OPTION_PAYMENT_CREDIT = 'CREDIT';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'company_id',
    ];

    /**
     * Get the company that owns the brand.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the purchases for the payment method.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

}
