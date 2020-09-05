<?php

namespace App\Http\Modules\Deposit;

use App\Http\Controllers\Controller;
use App\Http\Modules\Deposit\Deposit;
use Illuminate\Support\Facades\Schema;

class DepositController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $deposits = Deposit::query();

    return $this->showAll($deposits, Schema::getColumnListing((new Deposit)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Deposit\DepositRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(DepositRequest $request)
  {
    $deposit = Deposit::create(array_merge($request->validated(), [
      'date'          => now(),
      'created_by'    => auth()->user()->id,
    ]));

    $deposit->syncDepositImages($request->images_urls);

    return $this->showOne($deposit, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Deposit\Deposit  $deposit
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Deposit $deposit)
  {
    $deposit->load([
      'store:id,name',
      'creator:id,name',
    ]);

    return $this->showOne($deposit);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Deposit\DepositRequest  $request
   * @param  App\Http\Modules\Deposit\Deposit  $deposit
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(DepositRequest $request, Deposit $deposit)
  {
    $deposit->syncDepositImages($request->images_urls);
    $deposit->update($request->only('deposit_number', 'amount'));

    return $this->showOne($deposit);
  }
}
