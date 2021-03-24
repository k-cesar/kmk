<?php

namespace App\Http\Modules\StockCount;

use App\Http\Controllers\Controller;
use App\Http\Modules\Adjustment\Adjustment;

class StockCountAdjustmentController extends Controller
{
  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Stock\StockCountAdjustmentRequest  $request
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(StockCountAdjustmentRequest $request)
  {
    $stockCount = StockCount::find($request->stock_count_id);

    $adjustmentSaved = Adjustment::createFromStockCount($stockCount);

    if ($adjustmentSaved) {
      $stockCount->update(['status' => StockCount::OPTION_STATUS_CLOSED]);

      return $this->showMessage('Ajuste por Conteo creado exitosamente', 201);
    } else {
      return $this->errorResponse(500, "Ha ocurrido un error interno");
    }
  }

}
