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
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function index(){

        Validator::validate(request()->all(), [
            'store_id' => 'required|integer|store_visible',
        ]);
        
        $stockCounts = StockCount::where('store_id', request('store_id'));

        return $this->showAll($stockCounts, Arr::except(Schema::getColumnListing((new StockCount)->getTable()), 1));
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  App\Http\Modules\StockCount\StockCountRequest  $request
    * 
    * @return \Illuminate\Http\JsonResponse
    */
    public function store(StockCountRequest $request) {
        try {
            DB::beginTransaction();

            $stockCount = StockCount::create($request->validated());

            foreach ($request->products as $product) {
                StockCountDetail::create([
                    'stock_count_id' => $stockCount->id,
                    'product_id'     => $product['id'],
                    'quantity'       => $product['quantity'],
                ]);
            }

            DB::commit();
            return $this->showOne($stockCount, 201);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse(500, "Ha ocurrido un error interno al guardar stock");
        }
    }

    /**
    * Display the specified resource.
    *
    * @param  App\Http\Modules\StockCount\StockCount  $stockCount
    * @return \Illuminate\Http\JsonResponse
    */
    public function show(StockCount $stockCount) {
        
        $this->authorize('manage', $stockCount);

        return $this->showOne($stockCount);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Http\Modules\StockCount\StockCount  $stockCount
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(StockCount $stockCount)
    {
      $this->authorize('destroy', $stockCount);

      $stockCount->update(['status' => StockCount::OPTION_STATUS_CANCELLED]);

      return $this->showOne($stockCount);
    }
}
