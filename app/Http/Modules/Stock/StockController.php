<?php

namespace App\Http\Modules\Stock;

use App\Support\Helper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Validator;
use App\Http\Modules\StockCount\StockCount;

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
      'store_id' => 'required|integer|store_visible',
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

    $lastCount = StockCount::select('sc2.count_date AS last_count')
      ->from('stock_counts as sc2')
      ->whereRaw("sc2.id = MAX(sc.id)")
      ->withTrashed()
      ->toSql();

    $products = DB::table('products AS p')
      ->select('p.id AS product_id', 'p.description AS product_description', 'ss.quantity')
      ->selectRaw("($lastInput)")
      ->selectRaw("($lastOutput)")
      ->selectRaw("($lastCount)")
      ->join('stock_stores AS ss', 'ss.product_id', '=', 'p.id')
      ->leftJoin('stock_movements_detail AS smd', 'smd.product_id', '=', 'p.id')
      ->leftJoin('stock_counts_detail AS scd', 'scd.product_id', '=', 'p.id')
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
      ->leftJoin('stock_counts AS sc', function (JoinClause $leftJoin) {
        $leftJoin->on('scd.stock_count_id', '=', 'sc.id')
          ->where('sc.status', StockCount::OPTION_STATUS_CLOSED)
          ->where('sc.store_id', request('store_id'));
      })
      ->groupBy('p.id', 'ss.quantity')
      ->orderBy('p.description')
      ->where('ss.store_id', request('store_id'))
      ->whereRaw('UPPER(p.description) LIKE ?', [Helper::strToUpper(request('product_description', '%'))]);

    return $this->showAll($products);
  }
}
