<?php

namespace App\Http\Modules\StockCount;

use App\Traits\SecureDeletes;
use App\Http\Modules\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class StockCountDetail extends Model
{
    use SoftDeletes, SecureDeletes;

    protected $fillable = [
        'stock_count_id',
        'product_id',
        'quantity',
    ];

    protected $table = 'stock_counts_detail';

    protected $with = [
        'product'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
