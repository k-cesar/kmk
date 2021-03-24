<?php

namespace App\Policies;

use App\Http\Modules\User\User;
use App\Http\Modules\StockCount\StockCount;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockCountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the stockCount.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\StockCount\StockCount  $stockCount
     * 
     * @return mixed
     */
    public function manage(User $user, StockCount $stockCount)
    {
        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                return $user->company_id == $stockCount->store->company_id;
            } else {
                return $user->stores->where('id', $stockCount->store_id)->count();
            }
        }
            
        return true;
    }

    /**
     * Determine whether the user can destroy the stockCount.
     *
     * @param  \App\Http\Modules\User\User  $user
     * @param  \App\Http\Modules\StockCount\StockCount  $stockCount
     * 
     * @return mixed
     */
    public function destroy(User $user, StockCount $stockCount)
    {
        return $stockCount->status==StockCount::OPTION_STATUS_OPEN && $this->manage($user, $stockCount);
    }
}



