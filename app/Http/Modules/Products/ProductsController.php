<?php

namespace App\Http\Modules\Products;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\Products\Products;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Products::query();
        return $this->showAll($products, Schema::getColumnListing((new Products)->getTable()));
    }

    public function store(ProductsRequest $request) {
        $products = Products::create($request->validated());
        return $this->showOne($products, 201);
    }

    public function show(Products $product) {
        return $this->showOne($product);
    }

    public function update(ProductsRequest $request, Products $products) {
        $products->update($request->validated());
        return $this->showOne($products);
    }

    public function destroy(Products $product) {
        $product->secureDelete();
        return $this->showOne($product);
    }

    public function options(){
        $products = Products::select('id', 'description');
        return $this->showAll($products, Schema::getColumnListing((new Products)->getTable()));
    }
}
