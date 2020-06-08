<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
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
        $role = $request->role;

        if ($user->hasRole(config('app.role_super_admin_name')) || ($role->level >= $user->getMinimunRoleLevel())) {
            return $next($request);
        } else{
            abort(403);
        }
    }
}
