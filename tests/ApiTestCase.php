<?php

namespace Tests;

use App\Http\Modules\User\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class ApiTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Returns a User after sign in
     *
     * @param  \App\Http\Modules\User\User|null  $user
     * @return \App\Http\Modules\User\User

     */
    protected function signIn($user = null)
    {
        $user = $user ?: factory(User::class)->create();

        $token = JWTAuth::fromUser($user);

        $this->withHeaders(['Authorization' => "Bearer $token"]);
        
        return $user;
    }

    /**
     * Returns a User with Role after sign in
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @param  \App\Http\Modules\User\User|null  $user
     * @return \App\Http\Modules\User\User
     */
    protected function signInWithRole($role, $user = null)
    {
        $user = $user ?: factory(User::class)->create();

        $user->assignRole($role);

        $token = JWTAuth::fromUser($user);

        $this->withHeaders(['Authorization' => "Bearer $token"]);

        return $user;
    }

    /**
     * Returns a User with Permissions by routes names after sign in
     *
     * @param  array  $permissions
     * @param  \App\Http\Modules\User\User|null  $user
     * @return \App\Http\Modules\User\User
     */
    protected function signInWithPermissionsTo($routesNames, $user = null)
    {
        $user = $user ?: factory(User::class)->create();
        $permissions = Permission::whereIn('route_name', $routesNames)->get();

        $user->givePermissionTo($permissions);

        $token = JWTAuth::fromUser($user);

        $this->withHeaders(['Authorization' => "Bearer $token"]);

        return $user;
    }

    /**
     * Returns a Role with the Permissions by routes names
     *
     * @param  array  $routesNames
     * @param  \Spatie\Permission\Models\Role|null  $role
     * @return \Spatie\Permission\Models\Role
     */
    protected function getRoleWithPermissionsTo($routesNames, $role=null)
    {
        $role = $role ?: factory('Spatie\Permission\Models\Role')->create();
        $permissions = Permission::whereIn('route_name', $routesNames)->get();

        $role->givePermissionTo($permissions);

        return $role;
    }
   
}
