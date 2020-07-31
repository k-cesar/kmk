<?php

namespace App\Http\Modules\Presentations;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class Presentations extends Model
{
    use SecureDeletes;

    protected $filable = [
        'description',
        'price',
    ];
    
    protected $table = 'product_presentations';
}
