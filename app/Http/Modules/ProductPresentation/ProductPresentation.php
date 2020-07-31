<?php

namespace App\Http\Modules\ProductPresentation;

use App\Http\Modules\PresentationCombo\PresentationCombo;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPresentation extends Model
{
    use SoftDeletes, SecureDeletes;

    // protected $table = 'product_presentations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'price'
    ];

    /**
     * The presentationCombos that belong to the product_presentation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function presentationCombos()
    {
        return $this->belongsToMany(PresentationCombo::class, 'presentation_combos_detail');
    }
}
