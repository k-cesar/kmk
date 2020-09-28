<?php

namespace App\Http\Modules\CashAdjustment;

use Illuminate\Database\Eloquent\Model;

class CashAdjustment extends Model
{
    const OPTION_MODIFICATION_TYPE_PURCHASE = 'CASH PURCHASE';
    const OPTION_MODIFICATION_TYPE_DEPOSIT  = 'DEPOSIT';
    const OPTION_MODIFICATION_TYPE_OTHER    = 'OTHER';

    protected $table = 'store_turn_modifications';

    protected $fillable = [
        'store_id',
        'store_turn_id',
        'amount',
        'modification_type',
        'description',
    ];

    public static function getOptionCash()
    {
        return self::OPTION_MODIFICATION_TYPE_PURCHASE;
    }

    public static function getOptionsModificationTypes()
    {
        return [
            self::OPTION_MODIFICATION_TYPE_PURCHASE,
            self::OPTION_MODIFICATION_TYPE_DEPOSIT,
            self::OPTION_MODIFICATION_TYPE_OTHER,
        ];
    }
}
