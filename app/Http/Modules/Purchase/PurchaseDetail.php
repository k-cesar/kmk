<?php

namespace App\Http\Modules\Purchase;

use App\Http\Modules\Purchase\Purchase;
use Illuminate\Database\Eloquent\Model;
use App\Http\Modules\Presentation\Presentation;

class PurchaseDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_id',
        'presentation_id',
        'item_line',
        'quantity',
        'unit_price',
        'total',
    ];

    /**
     * Get the purchase that owns the purchase detail.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the presentation that owns the purchase detail.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function presentation()
    {
        return $this->belongsTo(Presentation::class);
    }
}
