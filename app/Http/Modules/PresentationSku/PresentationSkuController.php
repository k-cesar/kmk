<?php

namespace App\Http\Modules\PresentationSku;

use App\Http\Modules\PresentationSku\PresentationSku;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class PresentationSkuController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $presentationSkus = PresentationSku::query();

    return $this->showAll($presentationSkus, Schema::getColumnListing((new PresentationSku)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\PresentationSku\PresentationSkuRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(PresentationSkuRequest $request)
  {
    $presentationSku = PresentationSku::create($request->validated());

    return $this->showOne($presentationSku, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\PresentationSku\PresentationSku  $presentationSku
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(PresentationSku $presentationSku)
  {
    return $this->showOne($presentationSku);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\PresentationSku\PresentationSkuRequest  $request
   * @param  App\Http\Modules\PresentationSku\PresentationSku  $presentationSku
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(PresentationSkuRequest $request, PresentationSku $presentationSku)
  {
    $presentationSku->update($request->validated());

    return $this->showOne($presentationSku);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\PresentationSku\PresentationSku  $presentationSku
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(PresentationSku $presentationSku)
  {
    $presentationSku->secureDelete();

    return $this->showOne($presentationSku);
  }
}
