<?php

namespace App\Http\Modules\StoreFlag;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\StoreFlag\StoreFlag;

class StoreFlagController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $storeFlags = StoreFlag::query();

    return $this->showAll($storeFlags, Schema::getColumnListing((new StoreFlag)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\StoreFlag\StoreFlagRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(StoreFlagRequest $request)
  {
    $storeFlag = StoreFlag::create($request->validated());

    return $this->showOne($storeFlag, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\StoreFlag\StoreFlag  $storeFlag
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(StoreFlag $storeFlag)
  {
    return $this->showOne($storeFlag);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\StoreFlag\StoreFlagRequest  $request
   * @param  App\Http\Modules\StoreFlag\StoreFlag  $storeFlag
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(StoreFlagRequest $request, StoreFlag $storeFlag)
  {
    $storeFlag->update($request->validated());

    return $this->showOne($storeFlag);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\StoreFlag\StoreFlag  $storeFlag
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(StoreFlag $storeFlag)
  {
    $storeFlag->secureDelete();

    return $this->showOne($storeFlag);
  }
}
