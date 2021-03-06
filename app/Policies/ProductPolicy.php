<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\Product\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can create products.
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
     * Determine whether the user can manage the product.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\Product\Product  $product
     * 
     * @return mixed
     */
    public function manage(User $user, Product $product)
    {       
        if ($user->role->level > 1) {
            return $user->company_id == $product->company_id;
        }

        return true;
    }
}
