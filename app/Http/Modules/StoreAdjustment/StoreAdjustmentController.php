<?php

namespace App\Http\Modules\StoreAdjustment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Modules\StoreAdjustment\StoreAdjustment;

class StoreAdjustmentController extends Controller
{
    public function index()
    {
        $store = DB::table('stores as s')
        ->select(   
                    's.id as store_id',
                    's.name as store_name',
                    's.petty_cash_amount as petty_cash_amount',
                    'sm.date as sm_date',
                    'sm.description as sm_description',
                    'sm.origin_type as sm_origin_type',
                    'sm.movement_type as sm_movement_type',
                    'smd.created_at as smd_created_at',
                    'smd.product_id as smd_product_id',
                    'smd.quantity as quantity',
                )
        ->join('stock_movements as sm', 's.id', 'sm.store_id')
        ->join('stock_movements_detail as smd', 'sm.id', 'smd.stock_movement_id')
        ->join('turns as t', 'sm.turn_id', 't.id')
        ->where('t.is_active', 1)
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
                'sm_date' => $v->sm_date,
                'sm_description' => $v->sm_description,
                'sm_origin_type' => $v->sm_origin_type,
                'smd_created_at' => $v->smd_created_at,
                'smd_product_id' => $v->smd_product_id,
                'quantity' => $v->quantity,
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
                    'sm.date as sm_date',
                    'sm.description as sm_description',
                    'sm.origin_type as sm_origin_type',
                    'sm.movement_type as sm_movement_type',
                    'smd.created_at as smd_created_at',
                    'smd.product_id as smd_product_id',
                    'smd.quantity',
                )
        ->join('stock_movements as sm', 's.id', 'sm.store_id')
        ->join('stock_movements_detail as smd', 'sm.id', 'smd.stock_movement_id')
        ->join('turns as t', 'sm.turn_id', 't.id')
        ->where('s.id', $request)
        ->where('t.is_active', 1);

        return $this->showAll($store);
    }
}
