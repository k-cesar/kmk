<?php

namespace App\Http\Modules\Product;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\Product\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query();

        return $this->showAll($products, Schema::getColumnListing((new Product)->getTable()));
    }

    public function store(ProductRequest $request) {
        $product = Product::create($request->validated());

        return $this->showOne($product, 201);
    }

    public function show(Product $product) {
        return $this->showOne($product);
    }

    public function update(ProductRequest $request, Product $product) {
        $product->update($request->validated());

        return $this->showOne($product);
    }

    public function destroy(Product $product) {
        $product->secureDelete();

        return $this->showOne($product);
    }

    public function options(){
        $products = Product::select('id', 'description');

        return $this->showAll($products, Schema::getColumnListing((new Product)->getTable()));
    }
}
