<?php

namespace App\Http\Modules\StockCountsDetail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\StockCountsDetail\StockCountsDetail;

class StockCountsDetailController extends Controller
{
    public function index(){
        $stockCountsDetail = StockCountsDetail::query();

        return $this->showAll($stockCountsDetail, Schema::getColumnListing((new StockCountsDetail)->getTable()));
    }

    public function store(StockCountsDetailRequest $request) {
        $stockCountsDetail = StockCountsDetail::create($request->validated());
        return $this->showOne($stockCountsDetail, 201);
    }

    public function show(StockCountsDetail $stockCountsDetail) {
        return $this->showOne($stockCountsDetail);
    }

    public function update(StockCountsDetailRequest $request, StockCountsDetail $stockCountsDetail) {
        $stockCountsDetail->update($request->validated());
        return $this->showOne($stockCountsDetail);
    }

    public function destroy(StockCountsDetail $stockCountsDetail) {
        $stockCountsDetail->secureDelete();
        return $this->showOne($stockCountsDetail);
    }

    public function options(){
        $stockCountsDetail = StockCountsDetail::select('id', 'stock_count_id', 'product_id');
        return $this->showAll($stockCountsDetail, Schema::getColumnListing((new StockCountsDetail)->getTable()));
    }
}
