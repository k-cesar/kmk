<?php

namespace App\Http\Modules\Zone;

use App\Traits\SecureDeletes;
use App\Http\Modules\Store\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\Municipality\Municipality;

class Zone extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'municipality_id',
    ];

    /**
     * Get the municipality that owns the Zone.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Get the stores for the Zones.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stores()
    {
        return $this->hasMany(Store::class);
    }

}
