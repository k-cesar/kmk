<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\Client\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the client.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Client\Client  $client
     * 
     * @return mixed
     */
    public function manage(User $user, Client $client)
    {       
        if ($user->role->level > 1) {
            return $client->country_id == $user->company->country_id;
        }

        return true;
    }

    /**
     * Determine whether the user can manage the client.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Client\Client  $client
     * 
     * @return mixed
     */
    public function destroy(User $user, Client $client)
    {       
        return $user->role->level < 2;
    }
    
}
