<?php

namespace App\Http\Modules\StoreChain;

use App\Http\Modules\StoreFlag\StoreFlag;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
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

}
