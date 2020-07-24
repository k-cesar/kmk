<?php

namespace App\Http\Modules\ProductDepartment;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\ProductDepartment\ProductDepartment;

class ProductDepartmentController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $productDepartments = ProductDepartment::query();

    return $this->showAll($productDepartments, Schema::getColumnListing((new ProductDepartment)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\ProductDepartment\ProductDepartmentRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(ProductDepartmentRequest $request)
  {
    $productDepartment = ProductDepartment::create($request->validated());

    return $this->showOne($productDepartment, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\ProductDepartment\ProductDepartment  $productDepartment
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(ProductDepartment $productDepartment)
  {
    return $this->showOne($productDepartment);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\ProductDepartment\ProductDepartmentRequest  $request
   * @param  App\Http\Modules\ProductDepartment\ProductDepartment  $productDepartment
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(ProductDepartmentRequest $request, ProductDepartment $productDepartment)
  {    

    $productDepartment->update($request->validated());

    return $this->showOne($productDepartment);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\ProductDepartment\ProductDepartment  $productDepartment
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(ProductDepartment $productDepartment)
  {
    $productDepartment->secureDelete();

    return $this->showOne($productDepartment);
  }
}
