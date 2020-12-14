<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\SellPayment\SellPayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellPaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the sellPayment.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\SellPayment\SellPayment  $sellPayment
     * 
     * @return mixed
     */
    public function manage(User $user, SellPayment $sellPayment)
    {
        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                return $user->company_id == $sellPayment->sell->store->company_id;
            } else {
                return $user->stores->where('id', $sellPayment->sell->store_id)->count();
            }
        }
            
        return true;
    }
}



