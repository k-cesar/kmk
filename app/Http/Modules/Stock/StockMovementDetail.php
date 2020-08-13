<?php

namespace App\Http\Modules\Stock;

use Illuminate\Database\Eloquent\Model;

class StockMovementDetail extends Model
{
    /** 
     * Table Associated with the model
     */ 
    protected $table = 'stock_movements_detail';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stock_movement_id',
        'stock_store_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the stockMovement that owns the stockMovementDetail.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stockMovement()
    {
        return $this->belongsTo(StockMovement::class);
    }

    /**
     * Get the stockStore that owns the stockMovementDetail.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stockStore()
    {
        return $this->belongsTo(StockStore::class);
    }
}
