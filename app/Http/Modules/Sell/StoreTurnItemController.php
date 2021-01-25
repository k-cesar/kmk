<?php

namespace App\Http\Modules\Sell;

use Illuminate\Http\Request;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\Store\Store;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Modules\StoreTurn\StoreTurn;
use Illuminate\Database\Query\JoinClause;
use App\Http\Modules\Presentation\Presentation;
use App\Http\Modules\PresentationCombo\PresentationCombo;

class StoreTurnItemController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(Request $request, Store $store, Turn $turn)
  {
    $this->authorize('manage', $store);

    $storeTurn = StoreTurn::where('store_id', $store->id)
      ->where('turn_id', $turn->id)
      ->where('is_open', true)
      ->firstOrFail();

    $presentationsQuery = Presentation::select('id', 'description')
      ->selectRaw("COALESCE (pt.price, presentations.price) AS price, 'PRESENTATION' AS type")
      ->leftJoin('presentations_turns AS pt', function (JoinClause $leftJoin) use ($storeTurn) {
        $leftJoin->on('presentations.id', '=', 'pt.presentation_id')
          ->where('pt.turn_id', $storeTurn->turn_id);
      })
      ->visibleThroughCompany(auth()->user())
      ->filterByDescriptionOrSkuCode(request('presentation_description'), request('sku_code'));

    $combosQuery = PresentationCombo::select('presentation_combos.id', 'description')
      ->selectRaw("COALESCE (pcst.suggested_price, presentation_combos.suggested_price) AS price, 'COMBO' AS type")
      ->leftJoin('presentation_combos_stores_turns AS pcst', function (JoinClause $leftJoin) use ($storeTurn) {
        $leftJoin->on('presentation_combos.id', '=', 'pcst.presentation_combo_id')
          ->where('pcst.turn_id', $storeTurn->turn_id);
      })
      ->visibleThroughCompany(auth()->user())
      ->filterByDescriptionPresentationOrSku(request('presentation_combo_description'), request('presentation_description'), request('sku_code'));

    $items = DB::query()
      ->fromSub($presentationsQuery->union($combosQuery), 'items')
      ->orderBy('type', 'DESC')
      ->orderBy('description');
    
    return $this->showAll($items, ['id', 'description', 'type']);
  }
}
