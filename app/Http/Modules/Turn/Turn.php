<?php

namespace App\Http\Modules\Turn;

use App\Traits\SecureDeletes;
use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turn extends Model
{
    use SoftDeletes, SecureDeletes;

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

    /**
     * Scope a query to only include turns visibles by the user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Http\Modules\User\User $user
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, User $user)
    {
        if ($user->role->level > 1) {
            $visibleStores = $user->role->level == 2 ? $user->company->stores : $user->stores;

            return $query->whereIn('store_id', $visibleStores->pluck('id'));

        }

        return $query;
    }

}
