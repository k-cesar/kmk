<?php

namespace App\Http\Modules\Sell;

use App\Http\Modules\Sell\Sell;
use Illuminate\Database\Eloquent\Model;
use App\Http\Modules\Presentation\Presentation;
use App\Http\Modules\PresentationCombo\PresentationCombo;

class SellDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sell_id',
        'item_line',
        'presentation_id',
        'presentation_combo_id',
        'quantity',
        'price',
    ];

    /**
     * Get the sell that owns the sell detail.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sell()
    {
        return $this->belongsTo(Sell::class);
    }

    /**
     * Get the presentation that owns the sell detail.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function presentation()
    {
        return $this->belongsTo(Presentation::class);
    }

    /**
     * Get the presentationCombo that owns the sell detail.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function presentationCombo()
    {
        return $this->belongsTo(PresentationCombo::class);
    }
}
