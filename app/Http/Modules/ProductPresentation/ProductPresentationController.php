<?php

namespace App\Http\Modules\ProductPresentation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class ProductPresentationController extends Controller
{
    public function index(ProductPresentationRequest $request)
    {
        $where = [];

        if ($request->get('description')) {
            array_push($where, ['description', 'ilike', '%'.$request->get('description').'%']);
        }

        $presentations = ProductPresentation::where($where);
        return $this->showAll($presentations, Schema::getColumnListing((new ProductPresentation)->getTable()));
    }

    public function store(ProductPresentationRequest $request)
    {
        $productsPresentation = ProductPresentation::create($request->validated());
        return $this->showOne($productsPresentation, 201);
    }
    
    public function show(ProductPresentation $presentation)
    {
        return $this->showOne($presentation);
    }

    public function update(ProductPresentationRequest $request, ProductPresentation $productProductPresentation)
    {
        $productProductPresentation->update($request->validated());
        return $this->showOne($productProductPresentation);
    }

    public function destroy(ProductPresentation $presentation)
    {
        $presentation->secureDelete();
        return $this->showOne($presentation);
    }
}
