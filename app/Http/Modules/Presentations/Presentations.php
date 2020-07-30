<?php

namespace App\Http\Modules\Presentations;

use App\Traits\SecureDeletes;
use App\Http\Modules\Presentations\Presentations;
use Illuminate\Database\Eloquent\Model;

class Presentations extends Model
{
    use SecureDeletes;

    protected $table = 'product_presentations';

    protected $filable = [
        'description',
        'price'
    ];
}
