<?php

namespace App\Http\Modules\Purchase;

use App\Http\Modules\Product\Product;
use App\Http\Modules\Purchase\Purchase;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_id',
        'product_id',
        'item_line',
        'quantity',
        'unit_price',
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
     * Get the product that owns the purchase detail.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
