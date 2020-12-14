<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\Deposit\Deposit;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepositPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the deposit.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Deposit\Deposit  $deposit
     * 
     * @return mixed
     */
    public function manage(User $user, Deposit $deposit)
    {
        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                return $user->company_id == $deposit->store->company_id;
            } else {
                return $user->stores->where('id', $deposit->store_id)->count();
            }
        }
            
        return true;
    }
}



