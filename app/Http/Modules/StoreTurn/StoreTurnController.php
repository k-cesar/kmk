<?php

namespace App\Http\Modules\StoreTurn;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\StoreTurn\StoreTurn;

class StoreTurnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $storeTurns = StoreTurn::visibleThroughStore(auth()->user());

        return $this->showAll($storeTurns, Schema::getColumnListing((new StoreTurn)->getTable()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Modules\storeTurns\StoreTurnRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTurnRequest $request)
    {
        $storeTurn = StoreTurn::create($request->validated());

        return $this->showOne($storeTurn, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  App\Http\Modules\StoreTurn\StoreTurn  $storeTurn
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(StoreTurn $storeTurn)
    {
        $this->authorize('manage', $storeTurn);

        return $this->showOne($storeTurn);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Modules\StoreTurn\StoreTurnRequest  $request
     * @param  App\Http\Modules\StoreTurn\StoreTurn  $storeTurn
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreTurnRequest $request, StoreTurn $storeTurn)
    {
        $this->authorize('manage', $storeTurn);

        $storeTurn->update($request->validated());

        return $this->showOne($storeTurn, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Http\Modules\StoreTurn\StoreTurn  $storeTurn
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(StoreTurn $storeTurn)
    {
        $this->authorize('manage', $storeTurn);
        
        $storeTurn->secureDelete();

        return $this->showOne($storeTurn);
    }
}
