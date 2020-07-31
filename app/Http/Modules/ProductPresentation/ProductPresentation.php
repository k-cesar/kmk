<?php

namespace App\Http\Modules\ProductPresentation;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPresentation extends Model
{
    use SoftDeletes, SecureDeletes;

    // protected $table = 'product_presentations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $filable = [
        'description',
        'price'
    ];
}
