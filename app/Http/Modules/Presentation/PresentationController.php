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
        $presentations = Presentation::with('company:id,name')
            ->visibleThroughCompany(auth()->user())
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
        $this->authorize('create', Presentation::class);

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
        $this->authorize('manage', $presentation);
        
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
        $this->authorize('manage', $presentation);

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
        $this->authorize('manage', $presentation);

        $presentation->secureDelete();

        return $this->showOne($presentation);
    }

    /**
     * Display a compact list of the resource for select/combobox options.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function options()
    {
        $presentations = Presentation::select('id', 'description')
            ->withOut('product')
            ->visibleThroughCompany(auth()->user())
            ->filterByDescriptionOrSkuCode(request('presentation_description'), request('sku_code'));

      return $this->showAll($presentations, Schema::getColumnListing((new Presentation)->getTable()));
    }
}
