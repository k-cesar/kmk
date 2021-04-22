<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\Brand\Brand;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrandPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the brand.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Brand\Brand  $brand
     * 
     * @return mixed
     */
    public function manage(User $user, Brand $brand)
    {       
        if ($user->role->level > 1) {
            return $user->company_id == $brand->company_id;
        }

        return true;
    }
}
