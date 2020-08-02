<?php

namespace App\Http\Modules\ProductPresentation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class ProductPresentationController extends Controller
{
    public function index()
    {
        $productPresentations = ProductPresentation::query();

        return $this->showAll($productPresentations, Schema::getColumnListing((new ProductPresentation)->getTable()));
    }

    public function store(ProductPresentationRequest $request)
    {
        $productPresentation = ProductPresentation::create($request->validated());

        return $this->showOne($productPresentation, 201);
    }
    
    public function show(ProductPresentation $productPresentation)
    {
        return $this->showOne($productPresentation);
    }

    public function update(ProductPresentationRequest $request, ProductPresentation $productPresentation)
    {
        $productPresentation->update($request->validated());

        return $this->showOne($productPresentation);
    }

    public function destroy(ProductPresentation $productPresentation)
    {
        $productPresentation->secureDelete();

        return $this->showOne($productPresentation);
    }
}
