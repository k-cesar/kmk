<?php

namespace App\Http\Modules\Store;

use App\Traits\SecureDeletes;
use App\Http\Modules\Zone\Zone;
use App\Http\Modules\State\State;
use Illuminate\Database\Eloquent\Model;
use App\Http\Modules\StoreFlag\StoreFlag;
use App\Http\Modules\StoreType\StoreType;
use App\Http\Modules\StoreChain\StoreChain;
use App\Http\Modules\StoreFormat\StoreFormat;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\LocationType\LocationType;
use App\Http\Modules\Municipality\Municipality;
use App\Http\Modules\SocioeconomicLevel\SocioeconomicLevel;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\User\User;

class Store extends Model
{
    use SoftDeletes, SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'petty_cash_amount',
        'store_type_id',
        'store_chain_id',
        'store_flag_id',
        'location_type_id',
        'store_format_id',
        'size',
        'socioeconomic_level_id',
        'state_id',
        'municipality_id',
        'zone_id',
        'latitute',
        'longitude',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['storeType', 'storeChain', 'storeFlag', 'locationType', 'storeFormat', 'socioeconomicLevel', 'state', 'municipality', 'zone', 'turns'];

    /**
     * Get the storeType that owns the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storeType()
    {
        return $this->belongsTo(StoreType::class);
    }

    /**
     * Get the storeChain that owns the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storeChain()
    {
        return $this->belongsTo(StoreChain::class);
    }

    /**
     * Get the storeFlag that owns the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storeFlag()
    {
        return $this->belongsTo(StoreFlag::class);
    }

    /**
     * Get the storeFormat that owns the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storeFormat()
    {
        return $this->belongsTo(StoreFormat::class);
    }

    /**
     * Get the locationType that owns the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function locationType()
    {
        return $this->belongsTo(LocationType::class);
    }

    /**
     * Get the socioeconomicLevel that owns the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function socioeconomicLevel()
    {
        return $this->belongsTo(SocioeconomicLevel::class);
    }

    /**
     * Get the state that owns the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the municipality that owns the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Get the zone that owns the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * The users that belong to the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'store_users');
    }

    /**
     * Get the turns for the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function turns()
    {
        return $this->hasMany(Turn::class);
    }

    /**
     * The history/detail of turns that belong to the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function turnsHistory()
    {
        return $this->belongsToMany(Turn::class, 'store_turns');
    }
}
