<?php

namespace App\Http\Modules\ProductCategory;

use App\Http\Controllers\Controller;
use App\Http\Modules\ProductCategory\ProductCategory;

class ProductCategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $currencies = ProductCategory::paginate();

    return $this->showAll($currencies);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\ProductCategory\ProductCategoryRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(ProductCategoryRequest $request)
  {
    $productCategory = ProductCategory::create($request->validated());

    return $this->showOne($productCategory, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\ProductCategory\ProductCategory  $productCategory
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(ProductCategory $productCategory)
  {
    return $this->showOne($productCategory);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\ProductCategory\ProductCategoryRequest  $request
   * @param  App\Http\Modules\ProductCategory\ProductCategory  $productCategory
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(ProductCategoryRequest $request, ProductCategory $productCategory)
  {    

    $productCategory->update($request->validated());

    return $this->showOne($productCategory);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\ProductCategory\ProductCategory  $productCategory
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(ProductCategory $productCategory)
  {
    $productCategory->secureDelete();

    return $this->showOne($productCategory);
  }
}
