<?php

namespace App\Http\Modules\ProductCountries;

use App\Traits\SecureDeletes;
use App\Http\Modules\Country\Country;
use Illuminate\Database\Eloquent\Model;

class ProductCountries extends Model
{
    use SecureDeletes;

    protected $fillable = [
        'product_id',
        'country_id'
    ];

    protected $table = 'product_countries';

    protected $with = [
        'country'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
