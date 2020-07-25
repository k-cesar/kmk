<?php

namespace App\Http\Modules\Store;

use App\Http\Modules\Store\Store;
use App\Http\Controllers\Controller;
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
    $stores = Store::query();

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
    $store->secureDelete();

    return $this->showOne($store);
  }
}
