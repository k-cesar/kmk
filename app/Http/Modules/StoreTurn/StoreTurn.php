<?php

namespace App\Http\Modules\StoreTurn;

use Illuminate\Database\Eloquent\Model;

class StoreTurn extends Model
{
    protected $filable = [
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

    protected $with = [];
}
