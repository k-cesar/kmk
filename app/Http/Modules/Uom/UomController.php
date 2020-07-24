<?php

namespace App\Http\Modules\Uom;

use App\Http\Modules\Uom\Uom;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class UomController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $uoms = Uom::query();

    return $this->showAll($uoms, Schema::getColumnListing((new Uom)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Uom\UomRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(UomRequest $request)
  {
    $uom = Uom::create($request->validated());

    return $this->showOne($uom, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Uom\Uom  $uom
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Uom $uom)
  {
    return $this->showOne($uom);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Uom\UomRequest  $request
   * @param  App\Http\Modules\Uom\Uom  $uom
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(UomRequest $request, Uom $uom)
  {
    $uom->update($request->validated());

    return $this->showOne($uom);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Uom\Uom  $uom
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Uom $uom)
  {
    $uom->secureDelete();

    return $this->showOne($uom);
  }
}
