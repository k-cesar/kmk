<?php

namespace App\Http\Modules\Municipality;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\Municipality\Municipality;

class MunicipalityController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $municipalities = Municipality::query();

    return $this->showAll($municipalities, Schema::getColumnListing((new Municipality)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Municipality\MunicipalityRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(MunicipalityRequest $request)
  {
    $municipality = Municipality::create($request->validated());

    return $this->showOne($municipality, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Municipality\Municipality  $municipality
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Municipality $municipality)
  {
    return $this->showOne($municipality);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Municipality\MunicipalityRequest  $request
   * @param  App\Http\Modules\Municipality\Municipality  $municipality
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(MunicipalityRequest $request, Municipality $municipality)
  {
    $municipality->update($request->validated());

    return $this->showOne($municipality);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Municipality\Municipality  $municipality
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Municipality $municipality)
  {
    $municipality->secureDelete();

    return $this->showOne($municipality);
  }

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $municipalities = Municipality::select('id', 'name')
      ->withOut('state');

    return $this->showAll($municipalities, Schema::getColumnListing((new Municipality)->getTable()));
  }
}
