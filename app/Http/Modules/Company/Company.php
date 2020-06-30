<?php

namespace App\Http\Modules\Company;

use App\Traits\SecureDeletes;
use App\Http\Modules\User\User;
use App\Http\Modules\Country\Country;
use App\Http\Modules\Currency\Currency;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'reason',
        'nit',
        'phone',
        'country_id',
        'currency_id',
        'allow_add_products',
        'allow_add_stores',
        'is_electronic_invoice',
        'uses_fel',
    ];

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
     * @return @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

}
