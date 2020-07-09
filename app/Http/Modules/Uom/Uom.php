<?php

namespace App\Http\Modules\Uom;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class Uom extends Model
{
    use SecureDeletes;

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

}
