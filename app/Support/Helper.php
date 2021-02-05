<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class Helper
{
    /**
     * Make a utf8 string uppercase
     *
     * @param string $string
     * @param boolean $trimSpaces
     * @return string
     */
    public static function strToUpper($string, $trimSpaces=true)
    {
        if ($trimSpaces) {
            $string = preg_replace('/\s+/', ' ', trim($string));
        }
        
        return mb_strtoupper($string, 'utf-8');
    }

    /**
     * Make a utf8 string lowercase
     *
     * @param string $string
     * @param boolean $trimSpaces
     * @return string
     */
    public static function strToLower($string, $trimSpaces=true)
    {
        if ($trimSpaces) {
            $string = preg_replace('/\s+/', ' ', trim($string));
        }
        
        return mb_strtolower($string, 'utf-8');
    }

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

    /**
     * Encrypt any value
     * 
     * @param mixed $value Any value
     * @param string $passphrase Your password
     * 
     * @return string
     */
    public static function encrypt($value, string $passphrase)
    {
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $passphrase . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
        $data = ["ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt)];
        return json_encode($data);
    }

    /**
     * Decrypt a previously encrypted value
     * 
     * @param string $jsonStr Json stringified value
     * @param string $passphrase Your password
     * 
     * @return mixed
     */
    public static function decrypt(string $jsonStr, string $passphrase)
    {
        $json = json_decode($jsonStr, true);
        $salt = hex2bin($json["s"]);
        $iv = hex2bin($json["iv"]);
        $ct = base64_decode($json["ct"]);
        $concatedPassphrase = $passphrase . $salt;
        $md5 = [];
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return json_decode($data, true);
    }
}