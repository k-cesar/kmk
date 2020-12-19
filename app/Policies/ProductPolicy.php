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
        return $user->company->allow_add_products;
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
        return $user->role->level < 2;
    }
}
