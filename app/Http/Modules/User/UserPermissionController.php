<?php

namespace App\Http\Modules\User;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Modules\User\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class UserPermissionController extends Controller
{
    use ApiResponser;
    
    /**
     * Display a listing of the resource.
     *
     * @param  App\Http\Modules\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $this->authorize('manage', $user);

        $permissions = $user->getDirectPermissions()
            ->map(function (Permission $permission) {
                $permission->name = explode(' ', $permission->name)[0];

                return $permission;
            });

        return $this->showAll($permissions, Schema::getColumnListing((new Permission)->getTable()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Http\Modules\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {   
        $this->authorize('manage', $user);

        $permissions = Permission::whereIn('id', $request->permissions)
            ->where('level', '>=', auth()->user()->role->level)
            ->get()
            ->pluck('id');

        $user->syncPermissions($permissions);

        $permissions = $user->getDirectPermissions()
            ->map(function (Permission $permission) {
                $permission->name = explode(' ', $permission->name)[0];

                return $permission;
            });

        return $this->showAll($permissions, Schema::getColumnListing((new Permission)->getTable()));
    }
}
