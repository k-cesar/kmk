<?php

namespace App\Http\Modules\Stock;

use App\Http\Modules\Product\Product;
use App\Http\Modules\Store\Store;
use Illuminate\Database\Eloquent\Model;

class StockStore extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the store that owns the stockStore.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the product that owns the stock store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the stockMovementDetails for the stockStore.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function stockMovementDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
