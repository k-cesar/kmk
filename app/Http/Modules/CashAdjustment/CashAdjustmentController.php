<?php

namespace App\Http\Modules\CashAdjustment;

use App\Http\Controllers\Controller;

class CashAdjustmentController extends Controller
{
    /**
    * Store a newly created resource in storage.
    *
    * @param  App\Http\Modules\CashAdjustment\CashAdjustmentRequest  $request
    * 
    * @return \Illuminate\Http\JsonResponse
    */
    public function store(CashAdjustmentRequest $request)
    {
        $cashAdjustment = CashAdjustment::create($request->validated());

        $cashAdjustment->store->petty_cash_amount += $cashAdjustment->amount;
        $cashAdjustment->store->save();
        
        return $this->showOne($cashAdjustment, 201);
    }
}
