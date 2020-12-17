<?php

namespace App\Http\Modules\StoreTurn;

use App\Http\Modules\Sell\Sell;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\Purchase\Purchase;
use App\Http\Modules\StoreTurn\StoreTurn;
use App\Http\Modules\SellPayment\SellPayment;
use App\Http\Modules\PaymentMethod\PaymentMethod;
use App\Http\Modules\CashAdjustment\CashAdjustment;

class StoreTurnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $storeTurns = StoreTurn::visibleThroughStore(auth()->user());

        return $this->showAll($storeTurns, Schema::getColumnListing((new StoreTurn)->getTable()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Modules\storeTurns\StoreTurnRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTurnRequest $request)
    {
        $storeTurn = StoreTurn::create($request->validated());

        return $this->showOne($storeTurn, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  App\Http\Modules\StoreTurn\StoreTurn  $storeTurn
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(StoreTurn $storeTurn)
    {
        $this->authorize('manage', $storeTurn);

        return $this->showOne($storeTurn);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Modules\StoreTurn\StoreTurnRequest  $request
     * @param  App\Http\Modules\StoreTurn\StoreTurn  $storeTurn
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreTurnRequest $request, StoreTurn $storeTurn)
    {
        $this->authorize('manage', $storeTurn);

        $storeTurn->update($request->validated());

        $storeTurn->store->petty_cash_amount -= $storeTurn->expenses_in_not_purchases;
        $storeTurn->store->save();

        $storeTurn->card_sales_pos = $storeTurn->sellPayments()
            ->where('status', SellPayment::OPTION_STATUS_VERIFIED)
            ->where('payment_method_id', PaymentMethod::where('name', PaymentMethod::OPTION_PAYMENT_CARD)->first()->id)
            ->pluck('amount')
            ->sum();

        $storeTurn->expenses_in_purchases = Purchase::where('store_id', $storeTurn->store_id)
            ->where('payment_method_id', PaymentMethod::where('name', PaymentMethod::OPTION_PAYMENT_CASH)->first()->id)
            ->whereBetween('date', [$storeTurn->open_date, $storeTurn->close_date])
            ->pluck('total')
            ->sum();

        $storeTurn->deposits_total = $storeTurn->deposits
            ->pluck('amount')
            ->sum();
        
        $storeTurn->cash_sales = $storeTurn->sellPayments()
            ->where('status', SellPayment::OPTION_STATUS_VERIFIED)
            ->where('payment_method_id', PaymentMethod::where('name', PaymentMethod::OPTION_PAYMENT_CASH)->first()->id)
            ->whereHas('sell', function ($query) {
                $query->where('status', Sell::OPTION_STATUS_PAID)
                    ->where('is_to_collect', false);
            })
            ->pluck('amount')
            ->sum();

        $storeTurn->cash_collected_in_receivables = $storeTurn->sellPayments()
            ->where('status', SellPayment::OPTION_STATUS_VERIFIED)
            ->where('payment_method_id', PaymentMethod::where('name', PaymentMethod::OPTION_PAYMENT_CASH)->first()->id)
            ->whereHas('sell', function ($query) {
                $query->where('status', Sell::OPTION_STATUS_PAID)
                    ->where('is_to_collect', true);
            })
            ->pluck('amount')
            ->sum();

        $storeTurn->cash_adjustments_total = CashAdjustment::where('store_id', $storeTurn->store_id)
            ->whereBetween('created_at', [$storeTurn->open_date, $storeTurn->close_date])
            ->pluck('amount')
            ->sum();

        $storeTurn->unsetRelations();
        
        return $this->showOne($storeTurn, 200);
    }

}
