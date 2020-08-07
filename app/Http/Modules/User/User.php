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
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['company', 'stores'];

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
     * The stores that belong to the User.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_users');
    }

    /**
     * Returns all user actions grouped by permissions
     *
     * @return void
     */
    public function getActionsByPermissions()
    {
        $permissionsGrouped = $this->getAllPermissions()
            ->groupBy('group');

        $actionsGroups = [];

        foreach ($permissionsGrouped as $group => $permissionGrouped) {
            $actionsGroup = [
                'name'    => $group,
                'actions' => []
            ];

            foreach ($permissionGrouped as $permission) {
                $actionsGroup['actions'][] = explode(' ', $permission->name)[0];
            }

            $actionsGroups[] = $actionsGroup;
        }

        return $actionsGroups;
    }

}
