<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\Provider\Provider;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProviderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the provider.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Provider\Provider  $provider
     * 
     * @return mixed
     */
    public function manage(User $user, Provider $provider)
    {       
        if ($user->role->level > 1) {
            return $user->company_id == $provider->company_id;
        }

        return true;
    }
}
