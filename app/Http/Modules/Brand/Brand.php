<?php

namespace App\Http\Modules\Brand;

use App\Traits\SecureDeletes;
use App\Http\Modules\Maker\Maker;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'maker_id',
    ];

    /**
     * Get the maker that owns the brand.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function maker()
    {
        return $this->belongsTo(Maker::class);
    }

}
