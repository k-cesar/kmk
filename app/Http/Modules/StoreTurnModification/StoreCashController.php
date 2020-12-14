<?php

namespace App\Http\Modules\StoreTurnModification;

use App\Http\Modules\Store\Store;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class StoreCashController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $storesCash = Store::visible(auth()->user())
      ->select('id', 'name', 'petty_cash_amount')
      ->withOut('storeType', 'storeChain', 'storeFlag', 'locationType', 'storeFormat', 'socioeconomicLevel', 'state', 'municipality', 'zone', 'company', 'turns');

    return $this->showAll($storesCash, Schema::getColumnListing((new Store)->getTable()));
  }
}
