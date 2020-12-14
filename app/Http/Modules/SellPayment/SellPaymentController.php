<?php

namespace App\Http\Modules\SellPayment;

use App\Http\Modules\Sell\Sell;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SellPaymentController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    Validator::validate(request()->all(), [
      'store_id' => 'required|integer|store_visible',
    ]);
    
    $sellsPayments = SellPayment::query()
      ->with('sell.client:id,name')
      ->with('sell:id,description,date,total,status,client_id')
      ->whereHas('sell', function ($query) {
        return $query->where('store_id', request('store_id'))
          ->where('status', Sell::OPTION_STATUS_PENDING);
      })
      ->orderBy('created_at', 'DESC');

    return $this->showAll($sellsPayments);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\SellPayment\SellPaymentRequest  $request
   * @param  App\Http\Modules\SellPayment\SellPayment  $sellPayment
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(SellPaymentRequest $request, SellPayment $sellPayment)
  {
    $this->authorize('manage', $sellPayment);

    $sellPayment->update([
      'status'            => SellPayment::OPTION_STATUS_VERIFIED,
      'payment_method_id' => $request->payment_method_id,
    ]);

    $sellPayment->sell->status = Sell::OPTION_STATUS_PAID;

    if ($request->description) {
      $sellPayment->sell->description = $request->description;
    }

    $sellPayment->sell->save();

    return $this->showOne($sellPayment);
  }

}
