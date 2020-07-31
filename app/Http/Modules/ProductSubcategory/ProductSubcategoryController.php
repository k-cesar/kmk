<?php

namespace App\Http\Modules\ProductSubcategory;

use App\Http\Controllers\Controller;
use App\Http\Modules\ProductSubcategory\ProductSubcategory;
use Illuminate\Support\Facades\Schema;


class ProductSubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productSubcategories = ProductSubcategory::query();
        return $this->showAll($productSubcategories, Schema::getColumnListing((new ProductSubcategory)->getTable()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductSubcategoryRequest $request)
    {
        $productSubcategory = ProductSubcategory::create($request->validated());

        return $this->showOne($productSubcategory, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Modules\ProductSubcategory\ProductSubcategory  $productSubcategory
     * @return \Illuminate\Http\Response
     */
    public function show(ProductSubcategory $productSubcategory)
    {
        return $this->showOne($productSubcategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Modules\ProductSubcategory\ProductSubcategory  $productSubcategory
     * @return \Illuminate\Http\Response
     */
    public function update(ProductSubcategoryRequest $request, ProductSubcategory $productSubcategory)
    {
        $productSubcategory->update($request->validated());
        return $this->showOne($productSubcategory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Modules\ProductSubcategory\ProductSubcategory  $productSubcategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductSubcategory $productSubcategory)
    {
        $productSubcategory->secureDelete();
        return $this->showOne($productSubcategory);
    }

    public function options(){
        $productSubcategory = ProductSubcategory::select('id', 'name');

        return $this->showAll($productSubcategory, Schema::getColumnListing((new ProductSubcategory)->getTable()));
    }
}
