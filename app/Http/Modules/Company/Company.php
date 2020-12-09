<?php

namespace App\Http\Modules\Company;

use App\Traits\SecureDeletes;
use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Client\Client;
use App\Http\Modules\Country\Country;
use App\Http\Modules\Currency\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\PaymentMethod\PaymentMethod;

class Company extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'reason',
        'regime',
        'nit',
        'phone',
        'address',
        'country_id',
        'currency_id',
        'allow_add_products',
        'allow_add_stores',
        'is_electronic_invoice',
        'uses_fel',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['country', 'currency'];

    /**
     * Get the currency that owns the company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the country that owns the company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the users for the company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the payment methods for the company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    /**
     * The clients that belong to the client.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'company_clients')->withPivot('email', 'phone')->withTimestamps();
    }

    /**
     * Get the stores for the Company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    /**
     * Scope a query to only include companies visibles by the user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Http\Modules\User\User $user
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, User $user)
    {
        if ($user->role->level > 1) {
            return $query->where('id', $user->company_id);
        }

        return $query;
    }

}
