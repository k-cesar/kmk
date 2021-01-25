<?php

namespace App\Http\Modules\Presentation;

use App\Traits\SecureDeletes;
use App\Http\Modules\Turn\Turn;
use App\Traits\ResourceVisibility;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Product\Product;
use App\Http\Modules\Sell\SellDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\PresentationSku\PresentationSku;

class Presentation extends Model
{
    use SoftDeletes, SecureDeletes, ResourceVisibility;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
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
     * Get the company that owns the presentation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    /**
     * The turns that belong to the presentation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function turns()
    {
        return $this->belongsToMany(Turn::class, 'presentations_turns')->withPivot('price')->withTimestamps();
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

    /**
     * Sync the turn prices
     *
     * @param array $turnsPrices
     * @return array
     */
    public function syncTurnsPrices($turnsPrices)
    {
        $allowedTurns = Turn::visibleThroughStore(auth()->user())
            ->pluck('id');
        
        $turns = $this->turns()
            ->wherePivotIn('turn_id', $allowedTurns)
            ->get();

        $turnsPrices = collect($turnsPrices);

        $detached = $turns
            ->filter(function ($turn) use ($turnsPrices) {
                return !$turnsPrices->firstWhere('id', $turn->id);
            });

        $this->turns()->detach($detached);
        
        $updated = $turns
            ->filter(function ($turn) use ($turnsPrices) {
                $turnPrice = $turnsPrices->firstWhere('id', $turn->id);

                if ($turnPrice) {
                    return $turn->pivot->price != $turnPrice['price'];
                }

                return  false;
            })
            ->each(function ($turn) use ($turnsPrices) {
                $turnPrice = $turnsPrices->firstWhere('id', $turn->id);

                $turn->pivot->update(['price' => $turnPrice['price']]);
            });

        $attached = $turnsPrices
            ->filter(function ($turnPrice) use ($turns) {
                return !$turns->firstWhere('id', $turnPrice['id']);
            })
            ->each(function ($turnPrice) {
                $this->turns()->attach($turnPrice['id'], ['price' => $turnPrice['price']]);
            });
        
        return [
            'attached' => $attached->pluck('id')->toArray(),
            'updated'  => $updated->pluck('id')->toArray(),
            'detached' => $detached->pluck('id')->toArray(),
        ];
    }
    
}
