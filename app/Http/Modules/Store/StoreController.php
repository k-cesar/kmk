<?php

namespace App\Http\Modules\Store;

use App\Http\Modules\Store\Store;
use App\Http\Controllers\Controller;
use App\Http\Modules\Turn\Turn;
use Illuminate\Support\Facades\Schema;

class StoreController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $stores = Store::visible(auth()->user());

    return $this->showAll($stores, Schema::getColumnListing((new Store)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Store\StoreRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(StoreRequest $request)
  {
    $store = Store::create($request->validated());

    $store->turns()->create([
      'name'       => 'Estándar',
      'start_time' => '08:00:00',
      'end_time'   => '20:00:00',
      'is_active'  => 1,
      'is_default' => 1,
    ]);

    return $this->showOne($store, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Store\Store  $store
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Store $store)
  {
    $this->authorize('manage', $store);

    return $this->showOne($store);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Store\StoreRequest  $request
   * @param  App\Http\Modules\Store\Store  $store
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(StoreRequest $request, Store $store)
  {
    $this->authorize('manage', $store);

    $store->update($request->validated());

    return $this->showOne($store);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Store\Store  $store
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Store $store)
  {
    $this->authorize('manage', $store);

    $store->users()->sync([]);

    $store->secureDelete();

    return $this->showOne($store);
  }

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $stores = Store::select('id', 'name')
      ->withOut('storeType', 'storeChain', 'storeFlag', 'locationType', 'storeFormat', 'socioeconomicLevel', 'state', 'municipality', 'zone', 'company', 'turns')
      ->visible(auth()->user());

    return $this->showAll($stores, Schema::getColumnListing((new Store)->getTable()));
  }
}
