<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\Maker\Maker;
use Illuminate\Auth\Access\HandlesAuthorization;

class MakerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the maker.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Maker\Maker  $maker
     * 
     * @return mixed
     */
    public function manage(User $user, Maker $maker)
    {       
        if ($user->role->level > 1) {
            return $user->company_id == $maker->company_id;
        }

        return true;
    }
}
