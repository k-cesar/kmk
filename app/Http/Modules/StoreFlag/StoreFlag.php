<?php

namespace App\Http\Modules\StoreFlag;

use App\Traits\SecureDeletes;
use App\Http\Modules\Store\Store;
use Illuminate\Database\Eloquent\Model;
use App\Http\Modules\StoreChain\StoreChain;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreFlag extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'store_chain_id',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['storeChain'];

    /**
     * Get the storeChain that owns the storeFlag.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storeChain()
    {
        return $this->belongsTo(StoreChain::class);
    }

    /**
     * Get the stores for the StoreFlag.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stores()
    {
        return $this->hasMany(Store::class);
    }

}
