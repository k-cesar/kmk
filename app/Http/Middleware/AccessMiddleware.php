<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Models\Permission;

class AccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        $routeName = $request->route()->getName();
        $permissions = Permission::select('permissions.*')
            ->join('permission_routes', 'permissions.id', '=', 'permission_routes.permission_id')
            ->where('permission_routes.route', $routeName)
            ->get();

        if ($user->hasRole(config('app.role_super_admin_name')) || ($permissions->count() && $user->hasAnyPermission($permissions))) {
            return $next($request);
        } else{
            abort(403);
        }
    }
}
