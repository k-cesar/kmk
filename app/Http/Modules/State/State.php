<?php

namespace App\Http\Modules\State;

use App\Traits\SecureDeletes;
use App\Http\Modules\Region\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'region_id',
    ];

    /**
     * Get the region that owns the state.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

}
