<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\Company\Company;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the company.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Company\Company  $company
     * @return mixed
     */
    public function manage(User $user, Company $company)
    {
        if ($user->role->level <= 1) {
            return true;
        }

        return $user->company_id == $company->id;
    }
}
