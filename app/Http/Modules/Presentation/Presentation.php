<?php

namespace App\Http\Modules\Presentation;

use App\Http\Modules\Product\Product;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presentation extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'description',
        'price',
        'is_minimal_expression',
        'units',
    ];

     /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['product'];

    /**
     * Get the product that owns the presentation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    
}
