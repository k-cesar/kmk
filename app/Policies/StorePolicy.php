<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the store.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Store\Store  $store
     * 
     * @return mixed
     */
    public function manage(User $user, Store $store)
    {
        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                return $user->company_id == $store->company_id;
            } else {
                return $user->stores->where('id', $store->id)->count();
            }
        }
            
        return true;
    }
}
