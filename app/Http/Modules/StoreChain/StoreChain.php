<?php

namespace App\Http\Modules\StoreChain;

use App\Traits\SecureDeletes;
use App\Http\Modules\Store\Store;
use Illuminate\Database\Eloquent\Model;
use App\Http\Modules\StoreFlag\StoreFlag;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreChain extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the storeFlags for the storeChain.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function storeFlags()
    {
        return $this->hasMany(StoreFlag::class);
    }

    /**
     * Get the stores for the StoreChain.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stores()
    {
        return $this->hasMany(Store::class);
    }

}
