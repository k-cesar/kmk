<?php

namespace App\Http\Modules\StoreFormat;

use App\Http\Controllers\Controller;
use App\Http\Modules\StoreFormat\StoreFormat;

class StoreFormatController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $storeFormats = StoreFormat::paginate();

    return $this->showAll($storeFormats);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\StoreFormat\StoreFormatRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(StoreFormatRequest $request)
  {
    $storeFormat = StoreFormat::create($request->validated());

    return $this->showOne($storeFormat, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\StoreFormat\StoreFormat  $storeFormat
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(StoreFormat $storeFormat)
  {
    return $this->showOne($storeFormat);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\StoreFormat\StoreFormatRequest  $request
   * @param  App\Http\Modules\StoreFormat\StoreFormat  $storeFormat
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(StoreFormatRequest $request, StoreFormat $storeFormat)
  {
    $storeFormat->update($request->validated());

    return $this->showOne($storeFormat);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\StoreFormat\StoreFormat  $storeFormat
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(StoreFormat $storeFormat)
  {
    $storeFormat->secureDelete();

    return $this->showOne($storeFormat);
  }
}
