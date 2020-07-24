<?php

namespace App\Http\Modules\ProductSubCategories;

use App\Http\Controllers\Controller;
use App\Http\Modules\ProductSubCategories\ProductSubCategories;
use Illuminate\Http\Request;

class ProductSubCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $where = [];
        if ($request->get('name')) {
            array_push($where, ['name', 'ilike', '%'.$request->get('name').'%']);
        } 

        $productSubCategories = ProductSubCategories::where($where)->paginate();
        return $this->showAll($productSubCategories);
    }
     
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
    public function show(ProductSubCategories $productSubCategories)
    {
        return $this->showOne($productSubCategories);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Modules\ProductSubCategories\ProductSubCategories  $productSubCategories
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductSubCategories $productSubCategories)
    {
        return $this->showOne($productSubCategories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Modules\ProductSubCategories\ProductSubCategories  $productSubCategories
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductSubCategories $productSubCategories)
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
    public function destroy(ProductSubCategories $productSubCategories)
    {
        $productSubCategories->secureDelete();
        return $this->showOne($productSubCategories);
    }
}
