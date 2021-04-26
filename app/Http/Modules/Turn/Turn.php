<?php

namespace App\Http\Modules\Turn;

use App\Traits\SecureDeletes;
use App\Http\Modules\Store\Store;
use App\Traits\ResourceVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\Presentation\Presentation;
use App\Http\Modules\PresentationCombo\PresentationComboStoreTurn;

class Turn extends Model
{
    use SoftDeletes, SecureDeletes, ResourceVisibility;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'store_id',
        'start_time',
        'end_time',
        'is_active',
        'is_default',
    ];

    /**
     * Get the store that owns the turn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * The stores that belong to the turn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_turns');
    }

    /**
     * The presentations that belong to the turn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function presentations()
    {
        return $this->belongsToMany(Presentation::class, 'presentations_turns')->withPivot('price')->withTimestamps();
    }

    /**
     * Get the presentationComboStoreTurn for the turn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function presentationCombosStoresTurns()
    {
        return $this->hasMany(PresentationComboStoreTurn::class);
    }


}
