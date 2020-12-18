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

    const OPTION_STATUS_DTE_NA                    = 'NA';
    const OPTION_STATUS_DTE_PENDING_CERTIFICATION = 'PENDING_CERTIFICATION';
    const OPTION_STATUS_DTE_PENDING_CANCELLATION  = 'PENDING_CANCELLATION';
    const OPTION_STATUS_DTE_CANCELLED             = 'CANCELLED';
    const OPTION_STATUS_DTE_CERTIFIED             = 'CERTIFIED';

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
        'is_to_collect',
        'status',
        'status_dte',
        'invoice_link',
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
        return $this->hasOne(SellPayment::class)->withTrashed();
    }

    /**
     * Get the DTE's associated with the sell.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function dtes()
    {
        return $this->hasMany(DTE::class);
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
     * Returns all statuses DTE options availables
     *
     * @return array
     */
    public static function getOptionsStatusDTE()
    {
        return [
           self::OPTION_STATUS_DTE_NA,
           self::OPTION_STATUS_DTE_PENDING_CERTIFICATION,
           self::OPTION_STATUS_DTE_PENDING_CANCELLATION,
           self::OPTION_STATUS_DTE_CANCELLED,
           self::OPTION_STATUS_DTE_CERTIFIED,
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
        // For offline purpose
        $seller   = isset($params['seller_id']) ? User::find($params['seller_id']) : auth()->user();
        $clientId = $params['client_id'] ?? 0;  // 0 => Offline Client Id
        $date     = $params['date']      ?? now();

        $storeTurn = StoreTurn::find($params['store_turn_id']);

        $items = self::normalizePricesTurn($params['items'], $storeTurn->turn_id, $seller);

        $total = self::calculateTotal($items);

        $paymentMethod = PaymentMethod::find($params['payment_method_id']);

        if ($paymentMethod->name == PaymentMethod::OPTION_PAYMENT_CREDIT) {
            $sellStatus = self::OPTION_STATUS_PENDING;
            $isToCollect = true;
        } else {
            $sellStatus = self::OPTION_STATUS_PAID;
            $isToCollect = false;
        }

        $sell = self::create([
            'store_id'      => $params['store_id'],
            'client_id'     => $clientId,
            'description'   => $params['description'] ?? null,
            'date'          => $date,
            'total'         => $total,
            'seller_id'     => $seller->id,
            'is_to_collect' => $isToCollect,
            'status'        => $sellStatus,
            'status_dte'    => self::OPTION_STATUS_DTE_NA,
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

        $presentationsInCombos = self::getPresentationsInCombos($items->where('type', 'COMBO'), $storeTurn->turn_id);

        $presentations = $items->where('type', 'PRESENTATION')
            ->merge($presentationsInCombos);
        
        self::saveSellDetail($presentations, $sell, $stockMovement);

        self::saveSellPayment($sell, $paymentMethod);
        
        // If is not a offline client
        if ($clientId) {
            $storeTurn->store->company->clients()->syncWithoutDetaching([
                $params['client_id'] => [
                    'email' => $params['email'],
                    'phone' => $params['phone'],
                ]
            ]);
        }

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

        if ($storeTurn->store->company->uses_fel) {

            $sell->update(['status_dte' => Sell::OPTION_STATUS_DTE_PENDING_CERTIFICATION]);
            
            $dte = (new DTE())->fel($sell);

            if ($dte->certifier_success) {
              $sell->update([
                'status_dte'   => Sell::OPTION_STATUS_DTE_CERTIFIED,
                'invoice_link' => config('fel.invoiceBaseUrl').$dte->uuid,
              ]);
            }
        }
        
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
            ->select('p.id', 'p.product_id', 'p.units')
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
            $item['units']      = $itemStored->units ?? 1;

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
            ->select('p.id', 'p.product_id', 'p.units')
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
                    'units'      => $presentation->units,
                    'quantity'   => $combo['quantity'],
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

            StockMovementDetail::create([
                'stock_movement_id'     => $stockMovement->id,
                'stock_store_id'        => $stockStore->id,
                'product_id'            => $presentation['product_id'],
                'quantity'              => -1 * $presentation['quantity'] * $presentation['units'],
                'product_unit_price'    => $presentation['unit_price'] / $presentation['units'],
                'avg_product_unit_cost' => $stockStore->avg_product_unit_cost ?: $presentation['unit_price'] / $presentation['units'],
            ]);

            $stockStore->quantity -= $presentation['quantity'] * $presentation['units'];
            $stockStore->avg_product_unit_cost = $stockStore->calculateAvgProductUnitCost() ?: $presentation['unit_price'] / $presentation['units'];
            $stockStore->save();
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
            'payment_method_id' => $paymentMethod->id,
            'store_turn_id'     => $sell->store_turn_id,
            'amount'            => $sell->total,
            'card_four_digits'  => null,
            'authorization'     => null,
            'status'            => $status,
        ]);

        if ($paymentMethod->name == PaymentMethod::OPTION_PAYMENT_CASH) {
          $sell->store->petty_cash_amount += $sellPayment->amount;
          $sell->store->save();
        }

        return $sellPayment;
    }

    /**
     * Cancel a Sell
     *
     * @return void
     */
    public function cancel()
    {
        if ($this->sellPayment->paymentMethod->name == PaymentMethod::OPTION_PAYMENT_CASH) {
          $this->store->petty_cash_amount -= $this->total;
          $this->store->save();
        }

        $this->sellPayment->delete();
        $this->sellInvoice->delete();

        $this->update(['status' => Sell::OPTION_STATUS_CANCELLED]);

        if ($this->status_dte==self::OPTION_STATUS_DTE_CERTIFIED && $this->store->company->uses_fel) {

            $this->update(['status_dte' => Sell::OPTION_STATUS_DTE_PENDING_CANCELLATION]);

            $dte = (new DTE())->fel($this, true);

            if ($dte->certifier_success) {
              $this->update([
                'status_dte'   => Sell::OPTION_STATUS_DTE_CANCELLED,
                'invoice_link' => config('fel.invoiceBaseUrl').$dte->uuid,
              ]);

              $this->invoiceLink = config('fel.invoiceBaseUrl').$dte->uuid;
            }
        } 

        $this->delete();
    }
    
}
