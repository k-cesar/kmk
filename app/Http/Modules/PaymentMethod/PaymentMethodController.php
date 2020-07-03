<?php

namespace App\Http\Modules\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Http\Modules\PaymentMethod\PaymentMethod;

class PaymentMethodController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $paymentMethods = PaymentMethod::paginate();

    return $this->showAll($paymentMethods);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\PaymentMethod\PaymentMethodRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(PaymentMethodRequest $request)
  {
    $paymentMethod = PaymentMethod::create($request->validated());

    return $this->showOne($paymentMethod, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\PaymentMethod\PaymentMethod  $paymentMethod
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(PaymentMethod $paymentMethod)
  {
    return $this->showOne($paymentMethod);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\PaymentMethod\PaymentMethodRequest  $request
   * @param  App\Http\Modules\PaymentMethod\PaymentMethod  $paymentMethod
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(PaymentMethodRequest $request, PaymentMethod $paymentMethod)
  {
    $paymentMethod->update($request->validated());

    return $this->showOne($paymentMethod);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\PaymentMethod\PaymentMethod  $paymentMethod
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(PaymentMethod $paymentMethod)
  {
    $paymentMethod->secureDelete();

    return $this->showOne($paymentMethod);
  }
}
