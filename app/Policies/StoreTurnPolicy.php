<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\StoreTurn\StoreTurn;
use Illuminate\Auth\Access\HandlesAuthorization;

class StoreTurnPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the storeTurn.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\StoreTurn\StoreTurn  $storeTurn
     * 
     * @return mixed
     */
    public function manage(User $user, StoreTurn $storeTurn)
    {
        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                return $user->company_id == $storeTurn->store->company_id;
            } else {
                return $user->stores->where('id', $storeTurn->store_id)->count();
            }
        }
            
        return true;
    }
}



