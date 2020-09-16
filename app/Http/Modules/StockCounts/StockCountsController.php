<?php

namespace App\Http\Modules\StockCounts;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Http\Modules\StockCounts\StockCounts;
use App\Http\Modules\StockCountsDetail\StockCountsDetail;


class StockCountsController extends Controller
{

    public function index(StockCounts $request) {
        if (request('store_id') && request('name_user')){
            $sells = StockCounts::where('id', '=', request('store_id'))
            ->with('store')
            ->with('stock_counts')
            ->with('user')
            ->whereHas('user', function ($query) {
                return $query->where('name', 'like', '%'.request('name_user').'%');
            });   
            return $this->showAll($sells);
        }
        else {
            $stockCounts = StockCounts::query();
            return $this->showAll($stockCounts, Schema::getColumnListing((new StockCounts)->getTable()));
        }
    }

    public function store(StockCountsRequest $request) {
        try {
            DB::beginTransaction();

            $stockCount = StockCounts::create($request->validated());

            if(!empty($request->stock_counts_detail_product)) {
                $stockCountDetailProduct = $request->stock_counts_detail_product;
                $stockCountDetailQuantity = $request->stock_counts_detail_quantity;


                for($int = 0; $int < count($stockCountDetailProduct); $int++) {
                    $countsDetail = array(
                        'stock_count_id' => $stockCount->id,
                        'product_id' => $stockCountDetailProduct[$int],
                        'quantity' => $stockCountDetailQuantity[$int],
                    );
                    StockCountsDetail::create($countsDetail);
                }
            }
            

            DB::commit();
            return $this->showOne($stockCount, 201);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse(500, "Ha ocurrido un error interno al guardar stock");
        }
    }

    public function show(StockCounts $stockCount) {
        $stockCount->load('stock_counts');
        return $this->showOne($stockCount);
    }

    public function update(StockCountsRequest $request, StockCounts $stockCount, StockCountsDetail $stockCountsDetail) {
        try {
            DB::beginTransaction();

            $stockCount->update($request->validated());
            
            if(!empty($request->stock_counts_detail_product)) {
                $stockCountDetailProduct = $request->stock_counts_detail_product;
                $stockCountDetailQuantity = $request->stock_counts_detail_quantity;

                $arrDetail = array();

                for($int = 0; $int < count($stockCountDetailProduct); $int++) {
                    $countsDetail = array(
                        'stock_count_id' => $stockCount->id,
                        'product_id' => $stockCountDetailProduct[$int],
                        'quantity' => $stockCountDetailQuantity[$int],
                    );
                    $arrDetail[] = $countsDetail;
                }

                StockCountsDetail::where('stock_count_id', '=', $stockCount->id)->delete();
                
                foreach($arrDetail AS $key => $value) {
                    StockCountsDetail::create($value);
                }   
            }            

            DB::commit();
            return $this->showOne($stockCount);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse(500, "Ha ocurrido un error interno al guardar stock");
        }
    }

    public function destroy(StockCounts $stockCount) {
        $stockCount->secureDelete();
        return $this->showOne($stockCount);
    }

    public function options(){
        $stock = StockCounts::select('id', 'count_date', 'status');
        return $this->showAll($stock, Schema::getColumnListing((new StockCounts)->getTable()));
    }
}
