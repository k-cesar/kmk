<?php

namespace App\Http\Modules\Region;

use App\Http\Modules\Region\Region;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class RegionController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $regions = Region::query();

    return $this->showAll($regions, Schema::getColumnListing((new Region)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Region\RegionRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(RegionRequest $request)
  {
    $region = Region::create($request->validated());

    return $this->showOne($region, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Region\Region  $region
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Region $region)
  {
    return $this->showOne($region);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Region\RegionRequest  $request
   * @param  App\Http\Modules\Region\Region  $region
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(RegionRequest $request, Region $region)
  {
    $region->update($request->validated());

    return $this->showOne($region);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Region\Region  $region
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Region $region)
  {
    $region->secureDelete();

    return $this->showOne($region);
  }
}
