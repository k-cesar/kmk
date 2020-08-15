<?php

namespace App\Http\Modules\User;

use App\Traits\SecureDeletes;
use App\Http\Modules\Store\Store;
use Spatie\Permission\Models\Role;
use App\Http\Modules\Company\Company;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Modules\Stock\StockMovement;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles, SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'role_id',
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
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['role', 'company', 'stores'];

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
     * Get the company that owns the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the role that owns the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * The stores that belong to the User.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_users');
    }

    /**
     * Get the stockMovements for the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Returns all user actions grouped by permissions
     *
     * @return void
     */
    public function getActionsByPermissions()
    {
        $permissionsGrouped = $this->getDirectPermissions()
            ->groupBy('group');

        $actionsGroups = [];

        foreach ($permissionsGrouped as $group => $permissionGrouped) {
            $actionsGroup = [
                'name'    => $group,
                'actions' => []
            ];

            foreach ($permissionGrouped as $permission) {
                $actionsGroup['actions'][] = $permission->id;
            }

            $actionsGroups[] = $actionsGroup;
        }

        return $actionsGroups;
    }

}
