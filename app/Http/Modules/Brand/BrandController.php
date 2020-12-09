<?php

namespace App\Http\Modules\Brand;

use App\Http\Modules\Brand\Brand;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class BrandController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $brands = Brand::query();

    return $this->showAll($brands, Schema::getColumnListing((new Brand)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Brand\BrandRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(BrandRequest $request)
  {
    $brand = Brand::create($request->validated());

    return $this->showOne($brand, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Brand\Brand  $brand
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Brand $brand)
  {
    return $this->showOne($brand);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Brand\BrandRequest  $request
   * @param  App\Http\Modules\Brand\Brand  $brand
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(BrandRequest $request, Brand $brand)
  {
    $brand->update($request->validated());

    return $this->showOne($brand);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Brand\Brand  $brand
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Brand $brand)
  {
    $brand->secureDelete();

    return $this->showOne($brand);
  }

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $brands = Brand::select('id', 'name')
      ->withOut('maker');

    return $this->showAll($brands, Schema::getColumnListing((new Brand)->getTable()));
  }

}
