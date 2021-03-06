<?php

namespace App\Http\Modules\State;

use App\Http\Modules\State\State;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class StateController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $states = State::query();

    return $this->showAll($states, Schema::getColumnListing((new State)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\State\StateRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(StateRequest $request)
  {
    $state = State::create($request->validated());

    return $this->showOne($state, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\State\State  $state
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(State $state)
  {
    return $this->showOne($state);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\State\StateRequest  $request
   * @param  App\Http\Modules\State\State  $state
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(StateRequest $request, State $state)
  {
    $state->update($request->validated());

    return $this->showOne($state);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\State\State  $state
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(State $state)
  {
    $state->secureDelete();

    return $this->showOne($state);
  }

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $states = State::select('id', 'name')
      ->withOut('region');

    return $this->showAll($states, Schema::getColumnListing((new State)->getTable()));
  }

}
