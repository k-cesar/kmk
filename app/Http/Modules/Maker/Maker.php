<?php

namespace App\Http\Modules\Maker;

use App\Http\Modules\Brand\Brand;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class Maker extends Model
{
    use SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

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
