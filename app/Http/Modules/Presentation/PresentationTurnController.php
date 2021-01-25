<?php

namespace App\Http\Modules\Presentation;

use App\Http\Controllers\Controller;

class PresentationTurnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Presentation $presentation)
    {   
        $prices = collect();

        $presentation->load('company:id,name')
            ->load(['turns' => function ($query) {
                $query->visibleThroughStore(auth()->user());
            }])
            ->turns->each(function ($turn) use($prices) {
                $index = "{$turn->store_id}_{$turn->pivot->price}";

                $storePrice = $prices[$index] ?? [
                    'price'    => $turn->pivot->price,
                    'store_id' => $turn->store_id,
                ];

                $storePrice['turns'][] = $turn->only('id', 'name', 'start_time','end_time');

                $prices[$index] = $storePrice;
            });
        
        $presentation->unsetRelation('product')
            ->unsetRelation('turns')
            ->prices = $prices->values();

        return $this->showOne($presentation);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Modules\Presentation\PresentationTurnRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PresentationTurnRequest $request, Presentation $presentation)
    {
        $this->authorize('manageTurnsPrice', $presentation);

        $presentation->syncTurnsPrices($request->validated()['turnsPricesToSync']);

        return $this->showOne($presentation, 200);
    }
}
