<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class Helper
{
    

    /**
     * Convert permissions array or int or string to collection
     *
     * @param string|integer|array ...$permission
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function convertPermissionsToCollection(...$permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {

                if (empty($permission)) {
                    return false;
                }

                if (!$permission instanceof Permission) {
                    if (is_numeric($permission)) {
                        $permission = Permission::findById(intval($permission));
                    } elseif (is_string($permission)) {
                        $permission = Permission::findByName($permission);
                    }
                }

                return $permission;
            })
            ->filter(function ($permission)
            {
                return $permission instanceof Permission;
            });

        return $permissions;
    }

    /**
     * Check if a route is private
     *
     * @param string $routeName
     * 
     * @return boolean
     */
    public static function isPrivateRoute($routeName)
    {
        $route = Route::getRoutes()->getByName($routeName);

        if ($route) {
            // Check if the access middleware is attached to the route
            return in_array('access', $route->gatherMiddleware());
        }

        return false;
    }

    /**
     * Returns all unnecessary permissions
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getUnnecessaryPermissions()
    {
        $permissions = Permission::all()
            ->filter(function ($permission) {
                return !Helper::isPrivateRoute($permission->route_name);
            });

        return $permissions;
    }

    /**
     * Returns all private routes without permission
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getPrivateRoutesWithoutPermission()
    {
        $routes = collect(Route::getRoutes()->getRoutes())
            ->filter(function ($route){
                $permissionExists = Permission::where('route_name', $route->getName())->get()->count();
                // Check and Return if is a private route and don't have any permission
                return in_array('access', $route->gatherMiddleware()) && !$permissionExists;
            });
        
        return $routes;
    }

    /**
     * Returns the total permissions notifications, counting both 
     * unnecessary permissions and private routes without permission
     *
     * @return int
     */
    public static function getTotalPermissionsNotifications()
    {
        $unnecessaryPermissions = Helper::getUnnecessaryPermissions()->count();
        $privateRoutesWithoutPermission = Helper::getPrivateRoutesWithoutPermission()->count();
        $totalPermissionsNotifications = $unnecessaryPermissions + $privateRoutesWithoutPermission;
        
        return $totalPermissionsNotifications;
    }
}