<?php

namespace App\Http\Modules\ProductCountries;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Modules\ProductCountries\ProductCountries;
use Illuminate\Support\Facades\Schema;

class ProductCountriesController extends Controller
{
    public function index(ProductCountriesRequest $request)
    {
        $where = [];

        if($request->get('product_id')) {
            array_push($where, ['product_id', $request->get('product_id')]);
        }

        $productCountries = ProductCountries::where($where);
        return $this->showAll($productCountries, Schema::getColumnListing((new ProductCountries)->getTable()));
    }

    public function store(ProductCountriesRequest $request) {
        $productCountry = ProductCountries::create($request->validated());
        return $this->showOne($productCountry, 201);
    }

    public function show(ProductCountries $request) {
        return $this->showOne($request);
    }

    public function update(ProductCountriesRequest $request, ProductCountries $productCountries) {
        $productCountries->update($request->validated());
        return $this->showOne($productCountries);
    }

    public function destroy(ProductCountries $productCountries) {
        $productCountries->secureDelete();
        return $this->showOne($productCountries);
    }
}
