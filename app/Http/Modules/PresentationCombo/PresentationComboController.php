<?php

namespace App\Http\Modules\PresentationCombo;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\PresentationCombo\PresentationCombo;

class PresentationComboController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $presentationCombos = PresentationCombo::with('presentations:id,description')
      ->with('presentationCombosStoresTurns.store:id,name')
      ->with('presentationCombosStoresTurns.turn');

    return $this->showAll($presentationCombos, Schema::getColumnListing((new PresentationCombo)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\PresentationCombo\PresentationComboRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(PresentationComboRequest $request)
  {
    try {
      DB::beginTransaction();

      $presentationCombo = PresentationCombo::create($request->validated());
    
      $presentationCombo->presentations()->sync($request->presentations);

      $presentationCombo->syncPricesOfStoresAndTurns($request->prices);

      DB::commit();

      return $this->showOne($presentationCombo, 201);

    } catch (Exception $exception) {
      DB::rollback();

      Log::error($exception);

      return $this->errorResponse(500, "Ha ocurrido un error interno");
    }
    
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\PresentationCombo\PresentationCombo  $presentationCombo
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(PresentationCombo $presentationCombo)
  {
    $presentationCombo->load('presentations:id,description')
      ->load('presentationCombosStoresTurns.store:id,name')
      ->load('presentationCombosStoresTurns.turn');

    return $this->showOne($presentationCombo);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\PresentationCombo\PresentationComboRequest  $request
   * @param  App\Http\Modules\PresentationCombo\PresentationCombo  $presentationCombo
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(PresentationComboRequest $request, PresentationCombo $presentationCombo)
  {
    try {
      DB::beginTransaction();

      $presentationCombo->update($request->validated());
    
      $presentationCombo->presentations()->sync($request->presentations);

      $presentationCombo->syncPricesOfStoresAndTurns($request->prices);

      DB::commit();

      return $this->showOne($presentationCombo);

    } catch (Exception $exception) {
      DB::rollback();
      Log::error($exception);

      return $this->errorResponse(500, "Ha ocurrido un error interno");
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\PresentationCombo\PresentationCombo  $presentationCombo
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(PresentationCombo $presentationCombo)
  {
    $presentationCombo->secureDelete();

    return $this->showOne($presentationCombo);
  }
}
