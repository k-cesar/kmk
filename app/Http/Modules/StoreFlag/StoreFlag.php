<?php

namespace App\Http\Modules\StoreFlag;

use App\Http\Modules\StoreChain\StoreChain;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
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
     * Get the storeChain that owns the storeFlag.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storeChain()
    {
        return $this->belongsTo(StoreChain::class);
    }

}
