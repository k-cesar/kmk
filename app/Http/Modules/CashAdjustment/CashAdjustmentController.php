<?php

namespace App\Http\Modules\CashAdjustment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Modules\CashAdjustment\CashAdjustment;

class CashAdjustmentController extends Controller
{
    public function index()
    {
        $store = DB::table('stores as s')
        ->select(   
            's.id as store_id',
            's.name as store_name',
            's.petty_cash_amount as petty_cash_amount',
            'stm.amount as store_turn_m_amount',
            'stm.modification_type as store_turn_modification_type',
            'stm.description as store_turn_m_description'
        )
        ->join('store_turn_modifications as stm', 's.id', 'stm.store_id')
        ->get();
        
        $arrResponse = [];
        foreach($store AS $k => $v) {
            if(empty($arrResponse[$v->store_id])) {
                $arrResponse[$v->store_id] = [];
            }
            $arrResponse[$v->store_id][] = [
                'store_id' => $v->store_id,
                'store_name' => $v->store_name,
                'petty_cash_amount' => $v->petty_cash_amount,
                'store_turn_m_amount' => $v->store_turn_m_amount,
                'store_turn_modification_type' => $v->store_turn_modification_type,
                'store_turn_m_description' => $v->store_turn_m_description,
            ];
        }

        return $this->showAll($arrResponse);
    }

    public function show($request)
    {
        $store = DB::table('stores as s')
        ->select(   
            's.id as store_id',
            's.name as store_name',
            's.petty_cash_amount as petty_cash_amount',
            'stm.amount as store_turn_m_amount',
            'stm.modification_type as store_turn_modification_type',
            'stm.description as store_turn_m_description'
        )
        ->join('store_turn_modifications as stm', 's.id', 'stm.store_id')
        ->where('s.id', $request);

        return $this->showAll($store);
    }

    public function store(CashAdjustmentRequest $request)
    {
        $cashAdjustment = CashAdjustment::create(array_merge($request->validated(), [
            'modification_type'  => 'CASH PURCHASE',
        ]));
        
        return $this->showOne($cashAdjustment, 201);
    }
}
