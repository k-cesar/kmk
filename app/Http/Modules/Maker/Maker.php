<?php

namespace App\Http\Modules\Maker;

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

}
