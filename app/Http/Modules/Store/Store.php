<?php

namespace App\Http\Modules\Store;

use App\Support\Helper;
use App\Traits\SecureDeletes;
use App\Http\Modules\Sell\Sell;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\User\User;
use App\Http\Modules\Zone\Zone;
use App\Http\Modules\State\State;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Deposit\Deposit;
use App\Http\Modules\Product\Product;
use App\Http\Modules\Purchase\Purchase;
use Illuminate\Database\Eloquent\Model;
use App\Http\Modules\Stock\StockMovement;
use App\Http\Modules\StoreFlag\StoreFlag;
use App\Http\Modules\StoreTurn\StoreTurn;
use App\Http\Modules\StoreType\StoreType;
use App\Http\Modules\StoreChain\StoreChain;
use App\Http\Modules\StoreFormat\StoreFormat;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\LocationType\LocationType;
use App\Http\Modules\Municipality\Municipality;
use App\Http\Modules\PresentationCombo\PresentationCombo;
use App\Http\Modules\SocioeconomicLevel\SocioeconomicLevel;

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
        'company_id',
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
    protected $with = ['storeType', 'storeChain', 'storeFlag', 'locationType', 'storeFormat', 'socioeconomicLevel', 'state', 'municipality', 'zone', 'company', 'turns'];

    /**
     * Set the store's name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Helper::strToUpper($value);
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
     * Get the municipality that owns the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
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
     * Get the storeType that owns the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storeType()
    {
        return $this->belongsTo(StoreType::class);
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
        return $this->belongsToMany(User::class, 'store_users')->withTimestamps();
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

    /**
     * The presentationCombos that belong to the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function presentationCombos()
    {
        return $this->belongsToMany(PresentationCombo::class, 'presentation_combos_stores_turns');
    }

    /**
     * The products that belong to the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'stock_stores')->withPivot('id', 'quantity')->withTimestamps();
    }

    /**
     * Get the stockMovements for the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get the purchases for the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get the company for the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the sells for the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function sells()
    {
        return $this->hasMany(Sell::class);
    }

    /**
     * Get the storeTurns for the sell.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function storeTurns()
    {
        return $this->hasMany(StoreTurn::class);
    }

    /**
     * Get the deposits for the store.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Scope a query to only include stores visibles by the user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Http\Modules\User\User $user
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, User $user)
    {
        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                return $query->where('company_id', $user->company_id);
            } else {
                return $query->whereIn('id', $user->stores->pluck('id'));
            }
        }

        return $query;
    }
}
