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

        $this->actingAs($user);
        
        return $user;
    }

    /**
     * Returns a User with Permissions by routes names after sign in
     *
     * @param  array  $permissions
     * @param  \App\Http\Modules\User\User|null  $user
     * @return \App\Http\Modules\User\User
     */
    protected function signInWithPermissionsTo($routes, $user = null)
    {
        $user = $user ?: factory(User::class)->create();
        $permissions = Permission::select('permissions.*')
            ->join('permission_routes', 'permissions.id', '=', 'permission_routes.permission_id')
            ->whereIn('permission_routes.route', $routes)
            ->get();

        $user->givePermissionTo($permissions);

        $token = JWTAuth::fromUser($user);

        $this->withHeaders(['Authorization' => "Bearer $token"]);

        $this->actingAs($user);

        return $user;
    }

}
