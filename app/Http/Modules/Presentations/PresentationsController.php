<?php

namespace App\Http\Modules\Presentations;

use App\Http\Controllers\Controller;
use App\Http\Modules\Presentations\Presentations;
use Illuminate\Support\Facades\Schema;

class PresentationsController extends Controller
{
    public function index()
    {
        $presentations = Presentations::query();
        return $this->showAll($presentations, Schema::getColumnListing((new Presentations)->getTable()));
    }

    public function store(PresentationsRequest $request)
    {
        $presentation = Presentations::create($request->validated());
        return $this->showOne($presentation, 201);
    }
    
    public function show(Presentations $presentation)
    {
        return $this->showOne($presentation);
    }

    public function update(PresentationsRequest $request, Presentations $presentation)
    {
        $presentation->update($request->validated());
        return $this->showOne($presentation);
    }

    public function destroy(Presentations $presentation)
    {
        $presentation->secureDelete();
        return $this->showOne($presentation);
    }

    public function options()
    {
        $presentation = Presentations::select('id');
        return $this->showAll($presentation, Schema::getColumnListing((new Presentations)->getTable()));
    }
}
