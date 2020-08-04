<?php

namespace App\Http\Modules\PresentationSku;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\Presentation\Presentation;

class PresentationSku extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'description',
        'presentation_id',
        'seasonal_product',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['presentation'];

    /**
     * Get the presentation that owns the presentation_sku.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function presentation()
    {
        return $this->belongsTo(Presentation::class);
    }

}
