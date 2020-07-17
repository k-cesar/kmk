<?php

namespace App\Http\Modules\Zone;

use App\Traits\SecureDeletes;
use App\Http\Modules\Municipality\Municipality;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'municipality_id',
    ];

    /**
     * Get the municipality that owns the Zone.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

}
