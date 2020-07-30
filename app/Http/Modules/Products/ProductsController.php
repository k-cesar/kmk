<?php

namespace App\Http\Modules\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\Products\Products;

class ProductsController extends Controller
{
    public function index(ProductsRequest $request)
    {
        $where = [];

        $products = Products::where($where);
        return $this->showAll($products, Schema::getColumnListing((new Products)->getTable()));
    }

    public function store(ProductsRequest $request) {
        $products = Products::create($request->validated());
        return $this->showOne($products, 201);
    }

    public function show(Products $products) {
        return $this->showOne($products);
    }

    public function update(ProductsRequest $request, Products $products) {
        $products->update($request->validated());
        return $this->showOne($products);
    }

    public function destroy(Products $producs) {
        $producs->secureDelete();
        return $this->showOne($producs);
    }
}