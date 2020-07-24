<?php

namespace App\Http\Modules\Role;

use App\Support\Helper;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Middleware\RoleMiddleware;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    use ApiResponser;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(RoleMiddleware::class);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @param  Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function index(Role $role)
    {
        $permissions = $role->getAllPermissions();

        return $this->showAll($permissions, Schema::getColumnListing((new Permission)->getTable()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Role $role)
    {   
        $user = $request->user();
        $permissions = Helper::convertPermissionsToCollection($request->permissions);

        $permissionsCanBeAltered = $user->filterPermissionsCanAlter($permissions);
        $oldPermissions = $role->permissions;
        $permissionsUnaltered = $user->getRolePermissionsCannotAlter($role);

        if (!empty($permissionsCanBeAltered)) {
            $role->syncPermissions(array_unique(array_merge(
                $permissionsCanBeAltered->all(), 
                $permissionsUnaltered->all()
            )));

        }
        
        if (!$user->isValidPermissionsLevel($role, $permissions, $oldPermissions)){
            return $this->errorResponse(403, 'Forbidden');
        }
        
        $permissions = $role->getAllPermissions();

        return $this->showAll($permissions, Schema::getColumnListing((new Permission)->getTable()));
    }
}
