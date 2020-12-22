<?php

namespace App\Http\Modules\Presentation;

use App\Traits\SecureDeletes;
use App\Http\Modules\Product\Product;
use App\Http\Modules\Sell\SellDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\PresentationSku\PresentationSku;

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
        'is_grouping',
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

    /**
     * Get the sellDetails for the presentation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function sellDetails()
    {
        return $this->hasMany(SellDetail::class);
    }

    /**
     * Get the PresentationSkus for the presentation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function presentationSkus()
    {
        return $this->hasMany(PresentationSku::class);
    }

    /**
     * Scope a query to filter presentations by description or sku_code.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Http\Modules\User\User $user
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByDescriptionOrSkuCode($query, $description, $skuCode)
    {
        $query->when($description, function ($query) use ($description) {
            $query->orWhere('description', 'ilike', $description);
        })
        ->when($skuCode, function ($query) use ($skuCode) {
            $query->orWhereHas('presentationSkus', function ($subQuery) use ($skuCode) {
                $subQuery->where('code', 'ilike', $skuCode);
            });
        });

        return $query;
    }
    
}
