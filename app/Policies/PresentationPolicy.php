<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\Presentation\Presentation;
use Illuminate\Auth\Access\HandlesAuthorization;

class PresentationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can create presentations.
     *
     * @param  \App\Models\User  $user
     * 
     * @return bool
     */
    public function create(User $user)
    {
        if ($user->role->level > 1) {
            return $user->company->allow_add_products;
        }

        return true;
    }

    /**
     * Determine whether the user can manage the presentation.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Presentation\Presentation  $presentation
     * 
     * @return mixed
     */
    public function manage(User $user, Presentation $presentation)
    {       
        if ($user->role->level > 1) {
            return $user->company_id == $presentation->company_id;
        }

        return true;
    }
}
