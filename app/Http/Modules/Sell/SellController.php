<?php

namespace App\Http\Modules\Sell;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SellController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    Validator::validate(request()->all(), [
      'store_id' => 'required|exists:stores,id',
    ]);
    
    $sells = Sell::query()
      ->with('sellInvoice')
      ->with('sellPayment.paymentMethod:id,name')
      ->with('sellPayment:id,sell_id,payment_method_id')
      ->where('store_id', request('store_id'))
      ->whereHas('sellInvoice', function ($query) {
        return $query->where('invoice', 'LIKE', request('invoice', '%'));
      })
      ->withTrashed();

    return $this->showAll($sells);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Sell\SellRequest  $request
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(SellRequest $request)
  {
    try {
      DB::beginTransaction();

      $sell = Sell::buildAndSave($request->validated());

      DB::commit();

      return $this->showOne($sell, 201);

    } catch (Exception $exception) {
      DB::rollback();

      Log::error($exception);

      return $this->errorResponse(500, 'Ha ocurrido un error interno');
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Sell\Sell  $sell
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Sell $sell)
  {
    $sell->load('store:id,name')
      ->load('seller:id,name')
      ->load('sellInvoice')
      ->load('sellPayment.paymentMethod:id,name')
      ->load('sellDetails:id,sell_id,presentation_id,presentation_combo_id')
      ->load('sellDetails.presentation:id,description');

    return $this->showOne($sell);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Sell\Sell  $sell
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Sell $sell)
  {
    $sell->sellInvoice->delete();
    $sell->sellPayment->delete();
    $sell->delete();

    return $this->showOne($sell);
  }

}
