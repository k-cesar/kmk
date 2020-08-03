<?php

namespace App\Http\Modules\Product;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        try {
            DB::beginTransaction();

            $product = Product::create($request->validated());
            $product->productCountries()->sync($request->country);

            DB::commit();
            return $this->showOne($product, 201);
        } catch(Exception $exception) {
            DB::rollback();
            Log::error($exception);

            throw $exception;
            return $this->errorResponse(500, "Ha ocurrido un error interno");
        }
    }

    public function show(Product $product) {
        return $this->showOne($product);
    }

    public function update(ProductRequest $request, Product $product) {
        try {
            DB::beginTransaction();
            $product->update($request->validated());
            $product->productCountries()->sync($request->country);

            DB::commit();
            return $this->showOne($product);
        } catch (Exception $exception) {
            DB::rollback();
            Log::error($exception);
            return $this->errorResponse(500, "Ha ocurrido un error interno");
        }
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
