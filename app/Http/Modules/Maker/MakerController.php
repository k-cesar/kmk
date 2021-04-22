<?php

namespace App\Http\Modules\Maker;

use App\Http\Modules\Maker\Maker;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class MakerController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $makers = Maker::visibleThroughCompany(auth()->user())
      ->with('company:id,name');

    return $this->showAll($makers, Schema::getColumnListing((new Maker)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Maker\MakerRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(MakerRequest $request)
  {
    $maker = Maker::create($request->validated());

    return $this->showOne($maker, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Maker\Maker  $maker
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Maker $maker)
  {
    $this->authorize('manage', $maker);

    return $this->showOne($maker);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Maker\MakerRequest  $request
   * @param  App\Http\Modules\Maker\Maker  $maker
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(MakerRequest $request, Maker $maker)
  {
    $this->authorize('manage', $maker);

    $maker->update($request->validated());

    return $this->showOne($maker);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Maker\Maker  $maker
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Maker $maker)
  {
    $this->authorize('manage', $maker);
    
    $maker->secureDelete();

    return $this->showOne($maker);
  }

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $makers = Maker::select('id', 'name')
      ->visibleThroughCompany(auth()->user());

    return $this->showAll($makers, Schema::getColumnListing((new Maker)->getTable()));
  }
}
