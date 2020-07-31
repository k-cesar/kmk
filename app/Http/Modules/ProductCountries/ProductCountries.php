<?php

namespace App\Http\Modules\ProductCountries;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductCountries extends Model
{
    use SecureDeletes;

    protected $fillable = [
        'product_id',
        'country_id'
    ];

    protected $table = 'product_countries';
}
