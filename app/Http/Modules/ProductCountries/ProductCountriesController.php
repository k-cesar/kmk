<?php

namespace App\Http\Modules\ProductCountries;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Modules\ProductCountries\ProductCountries;
use Illuminate\Support\Facades\Schema;

class ProductCountriesController extends Controller
{
    public function index()
    {
        $where = [];
        if ($request->get('id')) {
            array_push($where, ['product_id', '=', "{$request->get('id')}"]);
        }
        $productCountries = ProductCountries::where($where);
        return $this->showAll($productCountries, Schema::getColumnListing((new ProductCountries)->getTable()));
    }

    public function store(ProductCountriesRequest $request) {
        $productCountry = ProductCountries::create($request->validated());
        return $this->showOne($productCountry, 201);
    }

    public function show(ProductCountries $productCountry) {
        return $this->showOne($productCountry);
    }

    public function update(ProductCountriesRequest $request, ProductCountries $productCountry) {
        $productCountry->update($request->validated());
        return $this->showOne($productCountry);
    }

    public function destroy(ProductCountries $productCountry) {
        $productCountry->secureDelete();
        return $this->showOne($productCountry);
    }

    public function options(){
        $productCountries = ProductCountries::select('id');
        return $this->showAll($productCountries, Schema::getColumnListing((new ProductCountries)->getTable()));
    }
}
