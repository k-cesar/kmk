<?php

namespace App\Http\Modules\StoreTurn;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\StoreTurn\StoreTurn;
use Illuminate\Support\Facades\Validator;

class StoreTurnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $storeTurns = StoreTurn::visible(auth()->user());

        return $this->showAll($storeTurns, Schema::getColumnListing((new StoreTurn)->getTable()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Modules\storeTurns\StoreTurnRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request['is_open'] = 1;
        $request['open_by'] = auth()->user()->id;
        $request['open_date'] = date('Y-m-d');
        
        $validator = Validator::make($request->all(), [
            'store_id'                  => 'required|integer|store_visible',
            'turn_id'                   => 'required|exists:turns,id',
            'open_petty_cash_amount'    => 'required|min:0',
            'is_open'                   => 'required|boolean',
            'open_by'                   => 'required|exists:users,id',
            'open_date'                 => 'required|date|date_format:Y-m-d',
        ]);
        
        if($validator->fails()) {
            return $this->errorResponse(422, 'Datos inválidos', $validator->errors());
        }
        
        $storeTurn = StoreTurn::create($validator->validated(), [
            'open_by'   => auth()->user()->id,
            'open_date' => date('Y-m-d'),
            'is_open'   => 1,
        ]);

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
        $openDate = date('Y-m-d', strtotime($storeTurn['open_date']));
        $request['open_petty_cash_amount'] = $storeTurn['open_petty_cash_amount'];
        $request['is_open'] = 0;
        $request['open_by'] = $storeTurn['open_by'];
        $request['open_date'] = $openDate;
        $request['closed_by'] = auth()->user()->id;
        $request['close_date'] = date('Y-m-d');

        $validator = Validator::make($request->all(), [
            'store_id'                  => 'required|integer|store_visible',
            'turn_id'                   => 'required|exists:turns,id',
            'open_petty_cash_amount'    => 'required|min:0',
            'is_open'                   => 'required|boolean',
            'open_by'                   => 'required|exists:users,id',
            'open_date'                 => 'required|date|date_format:Y-m-d',
            'closed_by'                 => 'required|exists:users,id',
            'close_date'                => 'required|date|date_format:Y-m-d',
            'closed_petty_cash_amount'  => 'required|min:0',
        ]);
        
        if($validator->fails()) {
            return $this->errorResponse(422, 'Datos inválidos', $validator->errors());
        }

        $storeTurn = StoreTurn::create($validator->validated(), [
            'is_open'                   => 0,
            'open_by'                   => $request['open_by'],
            'open_date'                 => $openDate,
            'closed_by'                 => auth()->user()->id,
            'close_date'                => date('Y-m-d'),
        ]);

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
        $storeTurn->secureDelete();

        return $this->showOne($storeTurn);
    }
}
