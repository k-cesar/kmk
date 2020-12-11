<?php

namespace App\Http\Modules\StoreTurnModification;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreTurnModification extends Model
{
    use SoftDeletes, SecureDeletes;

    const OPTION_MODIFICATION_TYPE_CASH_PURCHASE = 'CASH PURCHASE';
    const OPTION_MODIFICATION_TYPE_DEPOSIT       = 'DEPOSIT';
    const OPTION_MODIFICATION_TYPE_OTHER         = 'OTHER';

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'store_id',
        'store_turn_id',
        'amount',
        'modification_type',
        'description',
    ];

    /**
    * Returns all modification types options availables
    *
    * @return array
    */
    public static function getOptionsModificationTypes()
    {
        return [
            self::OPTION_MODIFICATION_TYPE_CASH_PURCHASE,
            self::OPTION_MODIFICATION_TYPE_DEPOSIT,
            self::OPTION_MODIFICATION_TYPE_OTHER,
        ];
    }
}
