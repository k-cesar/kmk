<?php

namespace App\Http\Modules\ProductSubCategories;

use App\Http\Controllers\Controller;
use App\Http\Modules\ProductSubCategories\ProductSubCategories;
use Illuminate\Support\Facades\Schema;


class ProductSubCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productSubCategories = ProductSubCategories::query();
        return $this->showAll($productSubCategories, Schema::getColumnListing((new ProductSubCategories)->getTable()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductSubCategoriesRequest $request)
    {
        $productSubCategories = ProductSubCategories::create($request->validated());
        return $this->showOne($productSubCategories, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Modules\ProductSubCategories\ProductSubCategories  $productSubCategories
     * @return \Illuminate\Http\Response
     */
    public function show(ProductSubCategories $productSubCategory)
    {
        return $this->showOne($productSubCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Modules\ProductSubCategories\ProductSubCategories  $productSubCategories
     * @return \Illuminate\Http\Response
     */
    public function update(ProductSubCategoriesRequest $request, ProductSubCategories $productSubCategories)
    {
        $productSubCategories->update($request->validated());
        return $this->showOne($productSubCategories);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Modules\ProductSubCategories\ProductSubCategories  $productSubCategories
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductSubCategories $productSubCategory)
    {
        $productSubCategory->secureDelete();
        return $this->showOne($productSubCategory);
    }

    public function options(){
        $productSubCategory = ProductSubCategories::select('id', 'name');
        return $this->showAll($productSubCategory, Schema::getColumnListing((new ProductSubCategories)->getTable()));
    }
}
