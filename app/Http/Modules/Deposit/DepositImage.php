<?php

namespace App\Http\Modules\Deposit;

use Illuminate\Database\Eloquent\Model;

class DepositImage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'base64_image',
        'deposit_id',
    ];

    /**
     * Get the base64 image.
     *
     * @param  string  $value
     * @return string
     */
    public function getBase64ImageAttribute($value)
    {
        return is_resource($value) ? stream_get_contents($value) : $value;
    }

    /**
     * Get the deposit that owns the deposit image.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

}
