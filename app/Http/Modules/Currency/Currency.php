<?php

namespace App\Http\Modules\Currency;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'symbol',
        'abbreviation',
        'description',
        'disabled',
    ];

}
