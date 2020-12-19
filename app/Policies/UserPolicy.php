<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can create users.
     *
     * @param  \App\Models\User  $user
     * 
     * @return bool
     */
    public function create(User $user)
    {
        return $user->company->allow_add_users;
    }

    /**
     * Determine whether the user can manage the user.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\User\User  $userToManage
     * 
     * @return mixed
     */
    public function manage(User $user, User $userToManage)
    {
        if ($user->role->level <= $userToManage->role->level) {
            if ($user->role->level <= 1) {
                return true;
            }

            return $user->company_id == $userToManage->company_id;
        }

        return false;
    }
}
