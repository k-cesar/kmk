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
        $permission = Permission::where('route_name', $routeName)->first();

        if ($user->hasRole(config('app.role_super_admin_name')) || ($permission && $user->can($permission->name))) {
            return $next($request);
        } else{
            abort(403);
        }
    }
}
