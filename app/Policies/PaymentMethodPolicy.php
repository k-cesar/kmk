<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Http\Modules\PaymentMethod\PaymentMethod;

class PaymentMethodPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the paymentMethod.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\PaymentMethod\PaymentMethod  $paymentMethod
     * 
     * @return mixed
     */
    public function manage(User $user, PaymentMethod $paymentMethod)
    {
        if ($user->role->level <= 1) {
            return true;
        }

        return $user->company_id == $paymentMethod->company_id;
    }
}



