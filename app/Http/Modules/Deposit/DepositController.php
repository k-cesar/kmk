<?php

namespace App\Http\Modules\Deposit;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    $deposits = Deposit::visibleThroughStore(auth()->user())
      ->with('creator:id,name')
      ->with('depositImages');

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
    try {
      DB::beginTransaction();

      $deposit = Deposit::create(array_merge($request->validated(), [
        'date'          => now(),
        'created_by'    => auth()->user()->id,
      ]));

      $deposit->depositImages()->createMany($request->validated()['images']);

      $deposit->store->petty_cash_amount += $deposit->amount;
      $deposit->store->save();

      DB::commit();

      return $this->showOne($deposit, 201);

    } catch (Exception $exception) {
      DB::rollback();

      Log::error($exception);

      return $this->errorResponse(500, 'Ha ocurrido un error interno');
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Deposit\Deposit  $deposit
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Deposit $deposit)
  {
    $this->authorize('manage', $deposit);

    $deposit->load([
      'store:id,name',
      'creator:id,name',
      'depositImages',
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
    $this->authorize('manage', $deposit);
    
    try {
      DB::beginTransaction();

      $deposit->store->petty_cash_amount += $request->get('amount') - $deposit->amount;
      $deposit->store->save();

      $deposit->depositImages()->delete();
      $deposit->depositImages()->createMany($request->validated()['images']);
      $deposit->update($request->only('deposit_number', 'amount'));

      DB::commit();

      return $this->showOne($deposit);

    } catch (Exception $exception) {
      DB::rollback();

      Log::error($exception);

      return $this->errorResponse(500, 'Ha ocurrido un error interno');
    }
  }
}
