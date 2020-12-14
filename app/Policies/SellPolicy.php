<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\Sell\Sell;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the sell.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Sell\Sell  $sell
     * 
     * @return mixed
     */
    public function manage(User $user, Sell $sell)
    {
        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                return $user->company_id == $sell->store->company_id;
            } else {
                return $user->stores->where('id', $sell->store_id)->count();
            }
        }
            
        return true;
    }
}



