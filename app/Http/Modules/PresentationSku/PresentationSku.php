<?php

namespace App\Http\Modules\PresentationSku;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\ProductPresentation\ProductPresentation;

class PresentationSku extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'description',
        'product_presentation_id',
        'seasonal_product',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['product_presentation'];

    /**
     * Get the product_presentation that owns the presentation_sku.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product_presentation()
    {
        return $this->belongsTo(ProductPresentation::class);
    }

}
