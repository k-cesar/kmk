<?php

namespace App\Http\Modules\SocioeconomicLevel;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocioeconomicLevel extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'is_all_countries',
    ];

}
