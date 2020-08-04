<?php

namespace App\Http\Modules\Presentation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class PresentationController extends Controller
{
    public function index()
    {
        $Presentations = Presentation::query();

        return $this->showAll($Presentations, Schema::getColumnListing((new Presentation)->getTable()));
    }

    public function store(PresentationRequest $request)
    {
        $Presentation = Presentation::create($request->validated());

        return $this->showOne($Presentation, 201);
    }
    
    public function show(Presentation $Presentation)
    {
        return $this->showOne($Presentation);
    }

    public function update(PresentationRequest $request, Presentation $Presentation)
    {
        $Presentation->update($request->validated());

        return $this->showOne($Presentation);
    }

    public function destroy(Presentation $Presentation)
    {
        $Presentation->secureDelete();

        return $this->showOne($Presentation);
    }
}
