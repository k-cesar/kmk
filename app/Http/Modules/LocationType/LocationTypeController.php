<?php

namespace App\Http\Modules\LocationType;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\LocationType\LocationType;

class LocationTypeController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $locationTypes = LocationType::query();

    return $this->showAll($locationTypes, Schema::getColumnListing((new LocationType)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\LocationType\LocationTypeRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(LocationTypeRequest $request)
  {
    $locationType = LocationType::create($request->validated());

    return $this->showOne($locationType, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\LocationType\LocationType  $locationType
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(LocationType $locationType)
  {
    return $this->showOne($locationType);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\LocationType\LocationTypeRequest  $request
   * @param  App\Http\Modules\LocationType\LocationType  $locationType
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(LocationTypeRequest $request, LocationType $locationType)
  {
    $locationType->update($request->validated());

    return $this->showOne($locationType);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\LocationType\LocationType  $locationType
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(LocationType $locationType)
  {
    $locationType->secureDelete();

    return $this->showOne($locationType);
  }

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $locationTypes = LocationType::select('id', 'name');

    return $this->showAll($locationTypes, Schema::getColumnListing((new LocationType)->getTable()));
  }
}
