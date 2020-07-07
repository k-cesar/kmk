<?php

namespace App\Http\Modules\User;

use App\Traits\SecureDeletes;
use Spatie\Permission\Models\Role;
use App\Http\Modules\Company\Company;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles, SoftDeletes, SecureDeletes;

    const OPTION_TYPE_ADMIN_MASTER     = 'ADMIN_MASTER';
    const OPTION_TYPE_ADMIN_ENTERPRISE = 'ADMIN_ENTERPRISE';
    const OPTION_TYPE_ADMIN_STORES     = 'ADMIN_STORES';
    const OPTION_TYPE_SELLER           = 'SELLER';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'email',
        'phone',
        'company_id',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Returns all types options availables
     *
     * @return array
     */
    public static function getOptionsTypes()
    {
        return [
            self::OPTION_TYPE_ADMIN_MASTER,
            self::OPTION_TYPE_ADMIN_ENTERPRISE,
            self::OPTION_TYPE_ADMIN_STORES,
            self::OPTION_TYPE_SELLER,
        ];
    }

    /**
     * Get the company that owns the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Returns all user permissions grouped by modules
     *
     * @return void
     */
    public function getPermissionsByModules()
    {
        $permissionsGrouped = $this->getAllPermissions()
            ->groupBy('group');

        $modules = [];

        foreach ($permissionsGrouped as $group => $permissionGrouped) {
            $module = [
                'name'        => $group,
                'permissions' => []
            ];

            foreach ($permissionGrouped as $permissions) {
                $module['permissions'][] = $permissions->name;
            }

            $modules[] = $module;
        }

        return $modules;
    }
    
    /**
     * Return only permissions that the user can alter based on 
     * permission level and the minimum level role of user
     *
     * @param \Illuminate\Support\Collection $permissions
     * @return \Illuminate\Support\Collection
     */
    public function filterPermissionsCanAlter($permissions)
    {
        $permissions = $permissions
            ->filter(function ($permission) {
                return $permission->level >= $this->getMinimunRoleLevel();
            });

        return $permissions;
    }

    /**
     * Returns role permissions that the user cannot alter based on 
     * permission level and the minimum level role of user
     * 
     * @param \Spatie\Permission\Models\Role $role
     * @param \Illuminate\Support\Collection
     */
    public function getRolePermissionsCannotAlter(Role $role)
    {
        $permissions = $role->getAllPermissions()
            ->filter(function ($permission) {
                return $permission->level < $this->getMinimunRoleLevel();
            });

        return $permissions;
    }

    /**
     * Validate permissions level to be altered with the minimum level role of user
     *
     * @param \Spatie\Permission\Models\Role $role
     * @param \Illuminate\Support\Collection  $newPermissions
     * @param \Illuminate\Support\Collection  $oldPermissions
     * @return bool
     */
    public function isValidPermissionsLevel(Role $role, $newPermissions, $oldPermissions)
    {

        // check if trying to add a permission with lower level than minimum level role of user
        foreach ($newPermissions as $permission) {
            if ($permission->level < $this->getMinimunRoleLevel()) {
                if (!$role->hasPermissionTo($permission)) {
                    return false;
                }
            }
        }

        // check if trying to remove a permission with lower level than minimum level role of user
        foreach ($oldPermissions as $permission) {
            if ($permission->level < $this->getMinimunRoleLevel()) {
                if (!$newPermissions->contains('name', $permission->name)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Returns the minimum level role of the user
     *
     * @return int
     */
    public function getMinimunRoleLevel()
    {
        $roles = $this->roles;

        if ($roles->isEmpty()) {
            return PHP_INT_MAX;
        }

        return $roles->min('level');
    }
    
}
