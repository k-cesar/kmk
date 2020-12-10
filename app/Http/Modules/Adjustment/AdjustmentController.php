<?php

namespace App\Http\Modules\Adjustment;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Modules\Stock\StockMovement;
use Illuminate\Support\Facades\Validator;
use App\Http\Modules\Adjustment\Adjustment;

class AdjustmentController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    Validator::validate(request()->all(), [
      'store_id' => 'required|integer|store_visible',
    ]);

    $adjustments = DB::table('stock_movements as sm')
      ->select('s.name as store_name', 'u.name as user_name', 'sm.date', 'sm.description')
      ->join('stores as s', 'sm.store_id', 's.id')
      ->join('users as u', 'sm.user_id', 'u.id')
      ->whereIn('origin_type', [StockMovement::OPTION_ORIGIN_TYPE_MANUAL_ADJUSTMENT, StockMovement::OPTION_ORIGIN_TYPE_COUNT])
      ->where('sm.store_id', request('store_id'))
      ->where('sm.date', 'LIKE', request('date', '%'))
      ->whereRaw('LOWER(sm.description) LIKE ?', [strtolower(request('description', '%'))])
      ->whereRaw('LOWER(u.name) LIKE ?', [strtolower(request('user_name', '%'))]);

    return $this->showAll($adjustments);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Adjustment\AdjustmentRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(AdjustmentRequest $request)
  {
    $adjustmentSaved = Adjustment::create($request->validated());

    if ($adjustmentSaved) {
      return $this->showMessage('Ajuste creado exitosamente', 201);
    } else {
      return $this->errorResponse(500, "Ha ocurrido un error interno");
    }
  }

}
