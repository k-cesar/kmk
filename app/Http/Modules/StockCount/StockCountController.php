<?php

namespace App\Http\Modules\StockCount;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Http\Modules\StockCount\StockCount;


class StockCountController extends Controller
{

    public function index(){

        Validator::validate(request()->all(), [
            'store_id' => 'required|integer|store_visible',
        ]);
        
        $stockCounts = StockCount::where('store_id', request('store_id'));

        return $this->showAll($stockCounts, Arr::except(Schema::getColumnListing((new StockCount)->getTable()), 1));
    }

    public function store(StockCountRequest $request) {
        try {
            DB::beginTransaction();

            $stockCount = StockCount::create($request->validated());

            if(!empty($request->stock_counts_detail_product)) {
                $stockCountDetailProduct = $request->stock_counts_detail_product;
                $stockCountDetailQuantity = $request->stock_counts_detail_quantity;


                for($int = 0; $int < count($stockCountDetailProduct); $int++) {
                    $countsDetail = array(
                        'stock_count_id' => $stockCount->id,
                        'product_id' => $stockCountDetailProduct[$int],
                        'quantity' => $stockCountDetailQuantity[$int],
                    );
                    StockCountDetail::create($countsDetail);
                }
            }
            

            DB::commit();
            return $this->showOne($stockCount, 201);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse(500, "Ha ocurrido un error interno al guardar stock");
        }
    }

    public function show(StockCount $stockCount) {
        
        $this->authorize('manage', $stockCount);

        return $this->showOne($stockCount);
    }

    public function update(StockCountRequest $request, StockCount $stockCount, StockCountDetail $stockCountsDetail) {

        $this->authorize('manage', $stockCount);

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

                StockCountDetail::where('stock_count_id', '=', $stockCount->id)->delete();
                
                foreach($arrDetail AS $key => $value) {
                    StockCountDetail::create($value);
                }   
            }            

            DB::commit();
            return $this->showOne($stockCount);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse(500, "Ha ocurrido un error interno al guardar stock");
        }
    }
}
