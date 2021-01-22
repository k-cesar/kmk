<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Http\Modules\PresentationSku\PresentationSku;

class PresentationSkuPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can create presentationSkus.
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
     * Determine whether the user can manage the presentationSku.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\PresentationSku\PresentationSku  $presentationSku
     * 
     * @return mixed
     */
    public function manage(User $user, PresentationSku $presentationSku)
    {       
        if ($user->role->level > 1) {
            return $user->company_id == $presentationSku->company_id;
        }

        return true;
    }
}
