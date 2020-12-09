<?php

namespace App\Http\Modules\Zone;

use App\Http\Modules\Zone\Zone;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class ZoneController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $zones = Zone::query();

    return $this->showAll($zones, Schema::getColumnListing((new Zone)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Zone\ZoneRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(ZoneRequest $request)
  {
    $zone = Zone::create($request->validated());

    return $this->showOne($zone, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Zone\Zone  $zone
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Zone $zone)
  {
    return $this->showOne($zone);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Zone\ZoneRequest  $request
   * @param  App\Http\Modules\Zone\Zone  $zone
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(ZoneRequest $request, Zone $zone)
  {
    $zone->update($request->validated());

    return $this->showOne($zone);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Zone\Zone  $zone
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Zone $zone)
  {
    $zone->secureDelete();

    return $this->showOne($zone);
  }

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $zones = Zone::select('id', 'name')
      ->withOut('municipality');

    return $this->showAll($zones, Schema::getColumnListing((new Zone)->getTable()));
  }

}
