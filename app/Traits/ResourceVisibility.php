<?php

namespace App\Traits;

use App\Http\Modules\User\User;

trait ResourceVisibility
{
    /**
     * Scope a query to only include resources visibles by the user filtered by their store_id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Http\Modules\User\User $user
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisibleThroughStore($query, User $user)
    {
        if ($user->role->level > 1) {
            $visibleStores = $user->role->level == 2 ? $user->company->stores : $user->stores;

            return $query->whereIn('store_id', $visibleStores->pluck('id'));

        }

        return $query;
    }

    /**
     * Scope a query to only include resources visibles by the user filtered by their company_id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Http\Modules\User\User $user
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisibleThroughCompany($query, User $user)
    {
        if ($user->role->level > 1) {
            return $query->whereIn('company_id', [0, $user->company_id]);
        }

        return $query;
    }
}