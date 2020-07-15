<?php

namespace App\Http\Modules\StoreFormat;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreFormat extends Model
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

}
