<?php

namespace App\Http\Modules\User;

use App\Support\Helper;
use App\Traits\SecureDeletes;
use App\Http\Modules\Sell\Sell;
use App\Http\Modules\Store\Store;
use Spatie\Permission\Models\Role;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Deposit\Deposit;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use App\Http\Modules\Stock\StockMovement;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    protected $with = ['role', 'company'];

    /**
     * Set the user's email.
     *
     * @param  string  $value
     * @return void
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = Helper::strToLower($value);
    }

    /**
     * Set the user's username.
     *
     * @param  string  $value
     * @return void
     */
    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = Helper::strToLower($value);
    }

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
        return $this->belongsTo(Company::class)->withTrashed();
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
        return $this->belongsToMany(Store::class, 'store_users')->withTimestamps();
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
     * Get the sells for the user (seller).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function sells()
    {
        return $this->hasMany(Sell::class);
    }

    /**
     * Get the deposits for the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Scope a query to only include users visibles by the user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Http\Modules\User\User $user
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, $user)
    {
        $query->whereIn('role_id', Role::select('id')->where('level', '>=', $user->role->level));

        if ($user->role->level > 1) {
            return $query->where('company_id', $user->company_id);
        }

        return $query;
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
