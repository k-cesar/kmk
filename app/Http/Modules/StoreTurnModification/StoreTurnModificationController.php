<?php

namespace App\Http\Modules\StoreTurnModification;

use App\Http\Controllers\Controller;

class StoreTurnModificationController extends Controller
{
    /**
    * Store a newly created resource in storage.
    *
    * @param  App\Http\Modules\StoreTurnModification\StoreTurnModificationRequest  $request
    * 
    * @return \Illuminate\Http\JsonResponse
    */
    public function store(StoreTurnModificationRequest $request)
    {
        $storeTurnModification = StoreTurnModification::create($request->validated());
        
        return $this->showOne($storeTurnModification, 201);
    }
}
