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
        Validator::validate(request()->all(), [
            'store_id' => 'required|exists:stores,id'
        ]);

        $store = DB::table('stores as s')
        ->select(   's.name as store_name',
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
        ->where('s.id', request('store_id'));

        return $this->showAll($store);
    }
}
