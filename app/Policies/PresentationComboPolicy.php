<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Http\Modules\PresentationCombo\PresentationCombo;

class PresentationComboPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can create presentationCombos.
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
     * Determine whether the user can manage the presentationCombo.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\PresentationCombo\PresentationCombo  $presentationCombo
     * 
     * @return mixed
     */
    public function manage(User $user, PresentationCombo $presentationCombo)
    {       
        if ($user->role->level > 1) {
            return $user->company_id == $presentationCombo->company_id;
        }

        return true;
    }
}
