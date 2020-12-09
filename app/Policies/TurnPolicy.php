<?php

namespace App\Policies;

use App\Http\Modules\Turn\Turn;
use App\Http\Modules\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TurnPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the user.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Turn\Turn  $turn
     * 
     * @return mixed
     */
    public function manage(User $user, Turn $turn)
    {
        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                return $user->company->stores->where('id', $turn->store_id)->count();
            } else {
                return $user->stores->where('id', $turn->store_id)->count();
            }
        }
            
        return true;
    }
}
