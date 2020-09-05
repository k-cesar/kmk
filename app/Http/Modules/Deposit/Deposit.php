<?php

namespace App\Http\Modules\Deposit;

use App\Traits\SecureDeletes;
use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;
use Illuminate\Database\Eloquent\Model;
use App\Http\Modules\StoreTurn\StoreTurn;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deposit extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'deposit_number',
        'amount',
        'date',
        'store_id',
        'store_turn_id',
        'created_by',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['depositImages:id,deposit_id,url'];

    /**
     * Get the store that owns the deposit.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the storeTurn that owns the deposit.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storeTurn()
    {
        return $this->belongsTo(StoreTurn::class);
    }

    /**
     * Get the user that owns the deposit.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the depositImages for the deposit.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function depositImages()
    {
        return $this->hasMany(DepositImage::class);
    }

    /**
     * Sync the deposit's images
     *
     * @param array $urlImages
     * 
     * @return array
     */
    public function syncDepositImages($imagesUrls)
    {
        $imagesUrls = collect($imagesUrls);

        $detached = $this->depositImages
            ->filter(function ($depositImage) use ($imagesUrls) {
                return !$imagesUrls->contains($depositImage->url);
            })->map(function ($depositImage) {
                $id = $depositImage->id;
                $depositImage->secureDelete();
                return $id;
            });
        
        $attached = $imagesUrls
            ->filter(function ($imageUrl) {
                return !$this->depositImages->contains(function ($depositImage) use ($imageUrl) {
                    return $imageUrl==$depositImage->url;
                });
            })->map(function ($imageUrl){
                return $this->depositImages()->save(new DepositImage(['url' => $imageUrl]))->id;
            });

        return [
            'attached' => $attached->toArray(),
            'detached' => $detached->toArray(),
        ];
    }

}
