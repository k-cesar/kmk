<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\Purchase\Purchase;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the purchase.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Purchase\Purchase  $purchase
     * 
     * @return mixed
     */
    public function manage(User $user, Purchase $purchase)
    {
        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                return $user->company_id == $purchase->store->company_id;
            } else {
                return $user->stores->where('id', $purchase->store_id)->count();
            }
        }
            
        return true;
    }
}



