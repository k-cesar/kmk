<?php

namespace App\Http\Modules\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\Products\Products;

class ProductsController extends Controller
{
    public function index(ProducsRequest $request)
    {
        $where = [];

        if($request->get('id')) {
            array_push($where, ['id', $request->get('id')]);
        }

        if($request->put('id')) {
            array_push($where, ['id', $request->get('id')]);
        }

        $products = Products::where($where);
        return $this->showAll($products, Schema::getColumnListing((new Products)->getTable()));
    }

    public function store(ProducsRequest $request) {
        $products = Products::create($request->validated());
        return $this->showOne($products, 201);
    }

    public function show(Products $request) {
        return $this->showOne($request);
    }

    public function update(ProducsRequest $request, Products $products) {
        $products->update($request->validated());
        return $this->showOne($products);
    }

    public function destroy(Products $producs) {
        $producs->secureDelete();
        return $this->showOne($producs);
    }
}
