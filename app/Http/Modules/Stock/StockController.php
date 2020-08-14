<?php

namespace App\Http\Modules\Stock;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    Validator::validate(request()->all(), [
      'store_id' => 'required|exists:stores,id',
    ]);

    $lastInput = StockMovement::select('smi2.date AS last_input')
      ->from('stock_movements as smi2')
      ->whereRaw("smi2.id = MAX(smi.id)")
      ->withTrashed()
      ->toSql();

    $lastOutput = StockMovement::select('smo2.date AS last_output')
      ->from('stock_movements as smo2')
      ->whereRaw("smo2.id = MAX(smo.id)")
      ->withTrashed()
      ->toSql();

    $products = DB::table('products AS p')
      ->select('p.id AS product_id', 'p.description AS product_description', 'ss.quantity')
      ->selectRaw("($lastInput)")
      ->selectRaw("($lastOutput)")
      ->selectRaw('p.deleted_at AS last_count')
      ->join('stock_stores AS ss', 'ss.product_id', '=', 'p.id')
      ->leftJoin('stock_movements_detail AS smd', 'smd.product_id', '=', 'p.id')
      ->leftJoin('stock_movements AS smi', function (JoinClause $leftJoin) {
        $leftJoin->on('smd.stock_movement_id', '=', 'smi.id')
          ->where('smi.movement_type', StockMovement::OPTION_MOVEMENT_TYPE_INPUT)
          ->where('smi.store_id', request('store_id'));
      })
      ->leftJoin('stock_movements AS smo', function (JoinClause $leftJoin) {
        $leftJoin->on('smd.stock_movement_id', '=', 'smo.id')
          ->where('smo.movement_type', StockMovement::OPTION_MOVEMENT_TYPE_OUTPUT)
          ->where('smo.store_id', request('store_id'));
      })
      ->groupBy('p.id', 'ss.quantity')
      ->orderBy('p.description')
      ->where('ss.store_id', request('store_id'))
      ->whereRaw('LOWER(p.description) LIKE ?', [strtolower(request('product_description', '%'))]);

    return $this->showAll($products);
  }
}
