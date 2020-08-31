<?php

namespace App\Http\Modules\StoreTurnModification;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class StoreTurnModification extends Model
{
    use SoftDeletes, SecureDeletes;

    protected $table = 'store_turn_modifications';

    protected $fillable = [
        'store_turn_id',
        'amount',
        'modification_type',
        'description',
    ];
}
