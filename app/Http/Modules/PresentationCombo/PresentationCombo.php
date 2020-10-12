<?php

namespace App\Http\Modules\PresentationCombo;

use Illuminate\Support\Arr;
use App\Http\Modules\Uom\Uom;
use App\Traits\SecureDeletes;
use Illuminate\Support\Facades\DB;
use App\Http\Modules\Sell\SellDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\Presentation\Presentation;

class PresentationCombo extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'uom_id',
        'minimal_expresion',
        'suggested_price',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['uom', 'presentations'];

    /**
     * Get the uom that owns the presentation_combo.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    /**
     * The presentations that belong to the presentation_combo.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function presentations()
    {
        return $this->belongsToMany(Presentation::class, 'presentation_combos_detail');
    }

    /**
     * The presentationComboStoreTurn that belong to the presentation_combo.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function presentationCombosStoresTurns()
    {
        return $this->hasMany(PresentationComboStoreTurn::class);
    }

    /**
     * Get the sellDetails for the presentationCombo.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function sellDetails()
    {
        return $this->hasMany(SellDetail::class);
    }

    /**
     * Sync the prices of the stores and turns
     *
     * @param array $prices
     * @return array
     */
    public function syncPricesOfStoresAndTurns($prices)
    {
        $prices = collect($prices);

        $detached = $this->presentationCombosStoresTurns
            ->filter(function ($presentationComboStoreTurn) use ($prices) {
                return !$prices->contains(function ($price, $key) use ($presentationComboStoreTurn){
                    return $price['store_id']==$presentationComboStoreTurn->store_id && in_array($presentationComboStoreTurn->turn_id, $price['turns']);
                });
            })->map(function ($presentationComboStoreTurn) {
                $id = $presentationComboStoreTurn->id;
                $presentationComboStoreTurn->delete();
                return $id;
            });
        
        $updated = $prices
            ->filter(function ($price) {
                return $this->presentationCombosStoresTurns()
                    ->where('store_id', $price['store_id'])
                    ->whereIn('turn_id', $price['turns'])
                    ->first();
            })->map(function ($price) {
                $ids = [];
                foreach ($price['turns'] as $turn_id) {
                    $presentationComboStoreTurn = $this->presentationCombosStoresTurns()
                    ->where('store_id', $price['store_id'])
                    ->where('turn_id', $turn_id)
                    ->first();

                    $presentationComboStoreTurn->update(['suggested_price' => $price['suggested_price']]);

                    $ids[] = $presentationComboStoreTurn->id;
                }

                return $ids;
            });
            
        $attached = $prices
            ->filter(function ($price) {
                return !$this->presentationCombosStoresTurns()
                    ->where('store_id', '=', $price['store_id'])
                    ->whereIn('turn_id', $price['turns'])
                    ->first();
            })->map(function ($price) {
                $ids = [];
                foreach ($price['turns'] as $turn_id) {
                    $id = DB::table('presentation_combos_stores_turns')->insertGetId([
                        'presentation_combo_id' => $this->id,
                        'store_id'              => $price['store_id'],
                        'turn_id'               => $turn_id,
                        'suggested_price'       => $price['suggested_price'],
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);

                    $ids[] = $id;
                }

                return $ids;
            });
        
        return [
            'attached' => Arr::flatten($attached),
            'updated'  => Arr::flatten($updated),
            'detached' => $detached->toArray(),
        ];
    }

}
