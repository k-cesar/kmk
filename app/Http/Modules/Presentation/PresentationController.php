<?php

namespace App\Http\Modules\Presentation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class PresentationController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function index()
    {
        $presentations = Presentation::orderBy('description')
            ->filterByDescriptionOrSkuCode(request('presentation_description'), request('sku_code'));

        return $this->showAll($presentations, Schema::getColumnListing((new Presentation)->getTable()));
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  App\Http\Modules\Presentation\PresentationRequest  $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function store(PresentationRequest $request)
    {
        $presentation = Presentation::create($request->validated());

        return $this->showOne($presentation, 201);
    }
    
    /**
    * Display the specified resource.
    *
    * @param  App\Http\Modules\Presentation\Presentation  $presentation
    * @return \Illuminate\Http\JsonResponse
    */
    public function show(Presentation $presentation)
    {
        return $this->showOne($presentation);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  App\Http\Modules\Presentation\PresentationRequest  $request
    * @param  App\Http\Modules\Presentation\Presentation  $presentation
    * @return \Illuminate\Http\JsonResponse
    */
    public function update(PresentationRequest $request, Presentation $presentation)
    {
        $presentation->update($request->validated());

        return $this->showOne($presentation);
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  App\Http\Modules\Presentation\Presentation  $presentation
    * @return \Illuminate\Http\JsonResponse
    */
    public function destroy(Presentation $presentation)
    {
        $presentation->secureDelete();

        return $this->showOne($presentation);
    }
}
