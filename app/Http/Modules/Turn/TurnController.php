<?php

namespace App\Http\Modules\Turn;

use App\Http\Controllers\Controller;
use App\Http\Modules\Turn\Turn;
use Illuminate\Support\Facades\Schema;

class TurnController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $turns = Turn::visible(auth()->user());

    return $this->showAll($turns, Schema::getColumnListing((new Turn)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Turn\TurnRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(TurnRequest $request)
  {
    $turn = Turn::create($request->validated());

    return $this->showOne($turn, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Turn\Turn  $turn
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Turn $turn)
  {
    $this->authorize('manage', $turn);

    return $this->showOne($turn);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Turn\TurnRequest  $request
   * @param  App\Http\Modules\Turn\Turn  $turn
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(TurnRequest $request, Turn $turn)
  {
    $this->authorize('manage', $turn);

    $turn->update($request->validated());

    return $this->showOne($turn);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Turn\Turn  $turn
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Turn $turn)
  {
    $this->authorize('manage', $turn);
    
    $turn->secureDelete();

    return $this->showOne($turn);
  }

}
