<?php

namespace App\Http\Modules\PresentationCombo;

use App\Http\Modules\Turn\Turn;
use App\Http\Modules\Store\Store;
use Illuminate\Database\Eloquent\Model;

class PresentationComboStoreTurn extends Model
{
    /** 
     * Table Associated with the model
     */ 
    protected $table = 'presentation_combos_stores_turns';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'presentation_combo_id',
        'store_id',
        'turn_id',
        'suggested_price'
    ];

    /**
     * The presentationCombo that belong to the presentation_combo_store_turn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function presentationCombo()
    {
        return $this->belongsTo(PresentationCombo::class);
    }
    
    /**
     * The stores that belong to the presentation_combo_store_turn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * The turns that belong to the presentation_combo_store_turn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function turn()
    {
        return $this->belongsTo(Turn::class);
    }

}
