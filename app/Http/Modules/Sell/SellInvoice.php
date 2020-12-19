<?php

namespace App\Http\Modules\Sell;

use App\Http\Modules\Sell\Sell;
use App\Http\Modules\Company\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellInvoice extends Model
{
    use SoftDeletes;

    const OPTION_CONCILATION_STATUS_PENDING = 'PENDING';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'invoice',
        'sell_id',
        'nit',
        'name',
        'date',
        'total',
        'concilation_status',
    ];

    /**
     * Set the invoice's nit.
     *
     * @param  string  $value
     * @return void
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['nit'] = strtoupper($value);
    }

    /**
     * Get the sell that owns the sell Invoice.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sell()
    {
        return $this->belongsTo(Sell::class)->withTrashed();;
    }

    /**
     * Get the company that owns the sell Invoice.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Gerate the next invoice number for the company
     *
     * @param  App\Http\Modules\Company\Company $company
     * @return string|int
     */
    public static function getNextInvoiceNumber($company) {
        $nextInvoiceNumber = SellInvoice::where('company_id', $company->id)
            ->withTrashed()
            ->count() + 1;

        return $nextInvoiceNumber;
    }
}
