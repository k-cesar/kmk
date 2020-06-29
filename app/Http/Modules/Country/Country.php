<?php

namespace App\Http\Modules\Country;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'currency_id',
    ];

    /**
     * Get the currency that owns the country.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

}
