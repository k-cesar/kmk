<?php

namespace App\Http\Modules\Uom;

use App\Support\Helper;
use App\Traits\SecureDeletes;
use App\Http\Modules\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Uom extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'abbreviation',
        'description',
    ];

    /**
     * Set the uom's name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Helper::strToUpper($value);
    }
    
    /**
     * Set the uom's abbreviation.
     *
     * @param  string  $value
     * @return void
     */
    public function setAbbreviationAttribute($value)
    {
        $this->attributes['abbreviation'] = Helper::strToUpper($value);
    }

    /**
     * Get the products for the uom.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
