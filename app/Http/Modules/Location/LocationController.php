<?php

namespace App\Http\Modules\Location;

use App\Http\Controllers\Controller;

class LocationController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $locations = Location::paginate();

    return $this->showAll($locations);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Location\LocationRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(LocationRequest $request)
  {
    $location = Location::create($request->validated());

    return $this->showOne($location, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Location\Location  $location
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Location $location)
  {
    return $this->showOne($location);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Location\LocationRequest  $request
   * @param  App\Http\Modules\Location\Location  $location
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(LocationRequest $request, Location $location)
  {
    $location->update($request->validated());

    return $this->showOne($location);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Location\Location  $location
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Location $location)
  {
    $location->secureDelete();

    return $this->showOne($location);
  }
}
