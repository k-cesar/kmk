<?php

namespace App\Http\Modules\StoreTurn;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Http\Modules\StoreTurnModification\StoreTurnModification;

class StoreTurn extends Model
{
    use SoftDeletes, SecureDeletes;

    protected $fillable = [
        'store_id',
        'turn_id',
        'open_petty_cash_amount',
        'open_by',
        'closed_by',
        'closed_petty_cash_amount',
        'open_date',
        'close_date',
        'is_open',
    ];

    protected $with = ['turn_modification'];

    public function turn_modification()
    {
        return $this->hasMany(StoreTurnModification::class, 'store_turn_id');
    }
}
