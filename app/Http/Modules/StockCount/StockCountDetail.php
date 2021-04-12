<?php

namespace App\Http\Modules\StockCount;

use App\Traits\SecureDeletes;
use App\Http\Modules\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class StockCountDetail extends Model
{
    use SoftDeletes, SecureDeletes;

    /** 
     * Table Associated with the model
     */ 
    protected $table = 'stock_counts_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stock_count_id',
        'product_id',
        'quantity',
        'quantity_stock',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'product'
    ];

    /**
     * Get the product that owns the stockCountDetail.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
