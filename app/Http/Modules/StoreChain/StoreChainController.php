<?php

namespace App\Http\Modules\StoreChain;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\StoreChain\StoreChain;

class StoreChainController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $storeChains = StoreChain::query();

    return $this->showAll($storeChains, Schema::getColumnListing((new StoreChain)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\StoreChain\StoreChainRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(StoreChainRequest $request)
  {
    $storeChain = StoreChain::create($request->validated());

    return $this->showOne($storeChain, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\StoreChain\StoreChain  $storeChain
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(StoreChain $storeChain)
  {
    return $this->showOne($storeChain);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\StoreChain\StoreChainRequest  $request
   * @param  App\Http\Modules\StoreChain\StoreChain  $storeChain
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(StoreChainRequest $request, StoreChain $storeChain)
  {
    $storeChain->update($request->validated());

    return $this->showOne($storeChain);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\StoreChain\StoreChain  $storeChain
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(StoreChain $storeChain)
  {
    $storeChain->secureDelete();

    return $this->showOne($storeChain);
  }

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $storeChains = StoreChain::select('id', 'name');

    return $this->showAll($storeChains, Schema::getColumnListing((new StoreChain)->getTable()));
  }
}
