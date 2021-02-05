<?php

namespace App\Http\Modules\Turn;

use App\Support\Helper;
use App\Traits\SecureDeletes;
use App\Http\Modules\Store\Store;
use App\Traits\ResourceVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turn extends Model
{
    use SoftDeletes, SecureDeletes, ResourceVisibility;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'store_id',
        'start_time',
        'end_time',
        'is_active',
        'is_default',
    ];

    /**
     * Set the turn's name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Helper::strToUpper($value);
    }

    /**
     * Get the store that owns the turn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * The stores that belong to the turn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_turns');
    }

}
