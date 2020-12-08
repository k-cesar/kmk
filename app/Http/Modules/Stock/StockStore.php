<?php

namespace App\Http\Modules\Stock;

use App\Http\Modules\Store\Store;
use Illuminate\Support\Facades\DB;
use App\Http\Modules\Product\Product;
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
        'avg_product_unit_cost',
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
        return $this->hasMany(StockMovementDetail::class);
    }

    /**
     * Calculate AvgProductUnitCost
     *
     * @return mixed
     */
    public function calculateAvgProductUnitCost()
    {
        if ($this->quantity <= 0 || config('database.default') == 'sqlite') {
            return 0;
        }

        $query = <<<SQL
        WITH cte AS ( 
            SELECT  SUM(quantity) OVER (ORDER BY smd.created_at DESC, smd.id DESC) AS total_quantity,
                    SUM(quantity * product_unit_price) OVER (ORDER BY smd.created_at DESC, smd.id DESC) / SUM(quantity) OVER (ORDER BY smd.created_at DESC, smd.id DESC) AS avg_product_unit_cost
            FROM stock_movements AS sm
            JOIN stock_movements_detail AS smd ON smd.stock_movement_id = sm.id
            WHERE   smd.stock_store_id = :stock_store_id
                AND sm.origin_type IN ('PURCHASE', 'TRANSFER')
                AND sm.movement_type = 'INPUT'
                AND sm.deleted_at IS NULL
        ) 
        ( 
            SELECT   * 
            FROM cte 
            WHERE total_quantity < :total_quantity
            ORDER BY total_quantity DESC 
            LIMIT 1
        ) 
        UNION ALL ( 
            SELECT * 
            FROM cte 
            WHERE total_quantity >= :total_quantity
            LIMIT 1
        )
        
SQL;

        $results = DB::select($query, [
            'stock_store_id' => $this->id,
            'total_quantity' => $this->quantity,
        ]);

        $result = collect($results)->last();

        $avgProductUnitCost = $result ? $result->avg_product_unit_cost : null;

        return $avgProductUnitCost;
    }
}
