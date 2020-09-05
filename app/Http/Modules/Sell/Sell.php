<?php

namespace App\Http\Modules\Sell;

use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;
use Illuminate\Support\Facades\DB;
use App\Http\Modules\Client\Client;
use App\Http\Modules\Stock\StockStore;
use Illuminate\Database\Eloquent\Model;
use App\Http\Modules\Stock\StockMovement;
use App\Http\Modules\StoreTurn\StoreTurn;
use Illuminate\Database\Query\JoinClause;
use App\Http\Modules\SellPayment\SellPayment;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\Stock\StockMovementDetail;
use App\Http\Modules\PaymentMethod\PaymentMethod;

class Sell extends Model
{
    use SoftDeletes;

    const OPTION_STATUS_PENDING   = 'PENDING';
    const OPTION_STATUS_CANCELLED = 'CANCELLED';
    const OPTION_STATUS_PAID      = 'PAID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id',
        'store_turn_id',
        'client_id',
        'description',
        'date',
        'total',
        'seller_id',
        'status',
    ];

    /**
     * Get the store that owns the sell.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the seller that owns the sell.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the client that owns the sell.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the storeTurn that owns the sell.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storeTurn()
    {
        return $this->belongsTo(StoreTurn::class);
    }

    /**
     * Get the sellDetails for the sell.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function sellDetails()
    {
        return $this->hasMany(SellDetail::class);
    }

    /**
     * Get the sellInvoice for the sell.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function sellInvoice()
    {
        return $this->hasOne(SellInvoice::class)->withTrashed();
    }

    /**
     * Get the sellPayment for the sell.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function sellPayment()
    {
        return $this->hasOne(SellPayment::class);
    }

    /**
     * Returns all statuses options availables
     *
     * @return array
     */
    public static function getOptionsStatus()
    {
        return [
           self::OPTION_STATUS_PENDING,
           self::OPTION_STATUS_CANCELLED,
           self::OPTION_STATUS_PAID,
        ];
    }

    /**
     * Build and save a sell from params given.
     *
     * @param array $params
     * 
     * @return App\Http\Modules\Sell\Sell|mixed
     */
    public static function buildAndSave(array $params)
    {
        $storeTurn = StoreTurn::where('store_id', $params['store_id'])
            ->where('turn_id', $params['turn_id'])
            ->where('is_open', true)
            ->firstOrFail();

        $seller = auth()->user();

        $items = self::normalizePricesTurn($params['items'], $params['turn_id'], $seller);

        $total = self::calculateTotal($items);

        $paymentMethod = PaymentMethod::find($params['payment_method_id']);

        $sellStatus = $paymentMethod->name == PaymentMethod::OPTION_PAYMENT_CREDIT ? 
            self::OPTION_STATUS_PENDING : self::OPTION_STATUS_PAID;

        $date = now();

        $sell = self::create([
            'store_id'      => $params['store_id'],
            'client_id'     => $params['client_id'],
            'description'   => $params['description'] ?? null,
            'date'          => $date,
            'total'         => $total,
            'seller_id'     => $seller->id,
            'status'        => $sellStatus,
            'store_turn_id' => $storeTurn->id,
        ]);

        $stockMovement = StockMovement::create([
          'user_id'       => $seller->id,
          'origin_id'     => $sell->id,
          'origin_type'   => StockMovement::OPTION_ORIGIN_TYPE_SELL,
          'date'          => $date,
          'movement_type' => StockMovement::OPTION_MOVEMENT_TYPE_OUTPUT,
          'store_id'      => $storeTurn->store_id,
        ]);

        $presentationsInCombos = self::getPresentationsInCombos($items->where('type', 'COMBO'), $params['turn_id']);
        
        $presentations = $items->where('type', 'PRESENTATION')
            ->merge($presentationsInCombos);
        
        self::saveSellDetail($presentations, $sell, $stockMovement);

        self::saveSellPayment($sell, $paymentMethod);
        
        $storeTurn->store->company->clients()->syncWithoutDetaching([
            $params['client_id'] => [
                'email' => $params['email'],
                'phone' => $params['phone'],
            ]
        ]);

        $invoiceNumber = SellInvoice::getNextInvoiceNumber($storeTurn->store->company);

        SellInvoice::create([
            'company_id'         => $storeTurn->store->company_id,
            'invoice'            => $invoiceNumber,
            'sell_id'            => $sell->id,
            'nit'                => $params['nit'],
            'name'               => $params['name'],
            'date'               => $date,
            'total'              => $total,
            'concilation_status' => SellInvoice::OPTION_CONCILATION_STATUS_PENDING,
        ]);
        
        return $sell;
    }

    /**
     * Normalize prices based on seller permissions to edit price and turn given 
     *
     * @param array $items
     * @param int $turnId
     * @param App\Http\Modules\User\User $seller
     * 
     * @return \Illuminate\Support\Collection
     */
    private static function normalizePricesTurn($items, $turnId, $seller)
    {
        $items = collect($items);

        $keepPrice = false;

        if ($seller->hasPermissionTo('Crear Editar Precios Durante la Venta')) {
            $keepPrice = true;
        }

        $presentationsStored = DB::table('presentations', 'p')
            ->select('p.id', 'p.product_id')
            ->selectRaw("COALESCE (tp.price, p.price) AS price")
            ->leftJoin("turns_products AS tp", function (JoinClause $leftJoin) use ($turnId) {
              $leftJoin->on('p.id', '=', "tp.presentation_id")
                ->where('tp.turn_id', $turnId);
            })
            ->whereIn('p.id', $items->where('type', 'PRESENTATION')->pluck('id'))
            ->whereNull('p.deleted_at')
            ->get();
        
        $combosStored = DB::table('presentation_combos', 'c')
            ->select('c.id')
            ->selectRaw("COALESCE (tc.suggested_price, c.suggested_price) AS price, NULL AS product_id")
            ->leftJoin("presentation_combos_stores_turns AS tc", function (JoinClause $leftJoin) use ($turnId) {
              $leftJoin->on('c.id', '=', "tc.presentation_combo_id")
                ->where('tc.turn_id', $turnId);
            })
            ->whereIn('c.id', $items->where('type', 'COMBO')->pluck('id'))
            ->whereNull('c.deleted_at')
            ->get();

        $items = $items->map(function ($item) use ($presentationsStored, $combosStored, $keepPrice) {

            $itemsStored = $item['type'] == 'PRESENTATION' ? $presentationsStored : $combosStored;

            $itemStored = $itemsStored->where('id', $item['id'])->first();

            $item['unit_price'] = $keepPrice ? $item['unit_price'] : $itemStored->price;
            $item['product_id'] = $itemStored->product_id;

            return $item;
        });

        return $items;
    }

   
    /**
     * Calculate the total of the sale
     *
     * @param \Illuminate\Support\Collection $presentations
     * @param \Illuminate\Support\Collection $combos
     * 
     * @return float
     */
    private static function calculateTotal($items)
    {
        return $items->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'] ;
        });
    }

    /**
     * Unpack the presentations that are in combos
     *
     * @param \Illuminate\Support\Collection $combos
     * @param int $turnId
     * 
     * @return void
     */
    private static function getPresentationsInCombos($combos, $turnId)
    {
        $presentations = $combos->map(function ($combo) use ($turnId) {
            $presentations = DB::table('presentations', 'p')
            ->select('p.id', 'p.product_id')
            ->selectRaw("COALESCE (tp.price, p.price) AS price")
            ->join('presentation_combos_detail AS cd', 'p.id', '=', 'cd.presentation_id' )
            ->leftJoin("turns_products AS tp", function (JoinClause $leftJoin) use ($turnId) {
              $leftJoin->on('p.id', '=', "tp.presentation_id")
                ->where('tp.turn_id', $turnId);
            })
            ->where('cd.presentation_combo_id', $combo['id'])
            ->get();

            $normalPricePresentations = $presentations->sum(function ($presentation) {
                return $presentation->price;
            });

            $percentageDiscount = $combo['unit_price'] / $normalPricePresentations;

            $presentations = $presentations->map(function ($presentation) use ($percentageDiscount, $combo) {
                return [
                    'id'         => $presentation->id,
                    'product_id' => $presentation->product_id,
                    'quantity'   => 1,
                    'unit_price' => $percentageDiscount * $presentation->price,
                    'combo_id'   => $combo['id'],
                ];
            });

            return $presentations;
        });

        return $presentations->flatten(1);
    }

    /**
     * Save sells's details
     *
     * @param \Illuminate\Support\Collection $presentations
     * @param App\Http\Modules\Sell\Sell $sell
     * @param App\Http\Modules\Stock\StockMovement $stockMovement
     * 
     * @return void
     */
    private static function saveSellDetail($presentations, $sell, $stockMovement)
    {
        $presentations->each(function ($presentation, $index) use($sell, $stockMovement) {
            SellDetail::create([
                'sell_id'               => $sell->id,
                'presentation_id'       => $presentation['id'],
                'presentation_combo_id' => $presentation['combo_id'] ?? null,
                'item_line'             => $index,
                'quantity'              => $presentation['quantity'],
                'price'                 => $presentation['unit_price'],
            ]);
            

            $stockStore = StockStore::firstOrCreate([
                'store_id'   => $sell->store_id,
                'product_id' => $presentation['product_id'],
            ]);

            $stockStore->quantity -= $presentation['quantity'];
            $stockStore->save();

            StockMovementDetail::create([
                'stock_movement_id' => $stockMovement->id,
                'stock_store_id'    => $stockStore->id,
                'product_id'        => $presentation['product_id'],
                'quantity'          => $presentation['quantity'],
            ]); 
        });
    }

    /**
     * Save Sell Payment
     *
     * @param App\Http\Modules\Sell\Sell $sell
     * @param App\Http\Modules\PaymentMethod\PaymentMethod $paymentMethod
     * 
     * @return App\Http\Modules\SellPayment\SellPayment
     */
    private static function saveSellPayment($sell, $paymentMethod)
    {
        $status = $paymentMethod->name == PaymentMethod::OPTION_PAYMENT_CREDIT ? 
            SellPayment::OPTION_STATUS_UNVERIFIED : SellPayment::OPTION_STATUS_VERIFIED;
        
        $sellPayment = SellPayment::create([
            'sell_id'           => $sell->id,
            'amount'            => $sell->total,
            'card_four_digits'  => null,
            'authorization'     => null,
            'status'            => $status,
            'payment_method_id' => $paymentMethod->id,
        ]);

        return $sellPayment;
    }
    
}
