<?php

namespace App\Http\Modules\Presentations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PresentationsController extends Controller
{
    public function index(PresentationsRequest $request)
    {
        $where = [];

        if ($request->get('description')) {
            array_push($where, ['description', 'ilike', '%'.$request->get('description').'%']);
        }

        $presentations = Presentations::where($where);
        return $this->showAll($presentations, Schema::getColumnListing((new Presentations)->getTable()));
    }

    public function store(PresentationsRequest $request)
    {
        $productsPresentation = Presentations::create($request->validated());
        return $this->showOne($productsPresentation, 201);
    }
    
    public function show(Presentations $presentation)
    {
        return $this->showOne($presentation);
    }

    public function update(PresentationsRequest $request, Presentations $productPresentations)
    {
        $productPresentations->update($request->validated());
        return $this->showOne($productPresentations);
    }

    public function destroy(Presentations $presentation)
    {
        $presentation->secureDelete();
        return $this->showOne($presentation);
    }
}
