<?php

namespace App\Http\Modules\StoreType;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\StoreType\StoreType;

class StoreTypeController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $storeTypes = StoreType::query();

    return $this->showAll($storeTypes, Schema::getColumnListing((new StoreType)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\StoreType\StoreTypeRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(StoreTypeRequest $request)
  {
    $storeType = StoreType::create($request->validated());

    return $this->showOne($storeType, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\StoreType\StoreType  $storeType
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(StoreType $storeType)
  {
    return $this->showOne($storeType);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\StoreType\StoreTypeRequest  $request
   * @param  App\Http\Modules\StoreType\StoreType  $storeType
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(StoreTypeRequest $request, StoreType $storeType)
  {
    $storeType->update($request->validated());

    return $this->showOne($storeType);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\StoreType\StoreType  $storeType
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(StoreType $storeType)
  {
    $storeType->secureDelete();

    return $this->showOne($storeType);
  }

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $storeTypes = StoreType::select('id', 'name');

    return $this->showAll($storeTypes, Schema::getColumnListing((new StoreType)->getTable()));
  }
}
