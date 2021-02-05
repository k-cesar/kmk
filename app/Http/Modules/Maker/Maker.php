<?php

namespace App\Http\Modules\Maker;

use App\Support\Helper;
use App\Traits\SecureDeletes;
use App\Http\Modules\Brand\Brand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maker extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Set the maker's name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Helper::strToUpper($value);
    }

    /**
     * Get the brands for the maker.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

}
