<?php

namespace App\Http\Modules\CashAdjustment;

use Illuminate\Database\Eloquent\Model;

class CashAdjustment extends Model
{
    const OPTION_PURCHASE   = 'CASH PURCHASE';
    const OPTION_DEPOSIT = 'DEPOSIT';
    const OPTION_OTHER = 'OTHER';

    protected $table = 'store_turn_modifications';

    protected $fillable = [
        'store_id',
        'store_turn_id',
        'amount',
        'modification_type',
        'description',
    ];

    public static function getModificationTypes()
    {
        return [
            self::OPTION_PURCHASE,
            self::OPTION_DEPOSIT,
            self::OPTION_OTHER,
        ];
    }
}
