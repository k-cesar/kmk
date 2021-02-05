<?php

namespace App\Http\Modules\Brand;

use App\Traits\SecureDeletes;
use App\Http\Modules\Maker\Maker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes, SecureDeletes;

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
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['maker'];

    /**
     * Set the brand's name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value)), 'utf-8');
    }

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
