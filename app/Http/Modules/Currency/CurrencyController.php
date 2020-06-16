<?php

namespace App\Http\Modules\Currency;

use App\Http\Controllers\Controller;
use App\Http\Modules\Currency\Currency;

class CurrencyController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $currencies = Currency::paginate();

    return $this->showAll($currencies);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Currency\CurrencyRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(CurrencyRequest $request)
  {
    $currency = Currency::create($request->validated());

    return $this->showOne($currency, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Currency\Currency  $currency
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Currency $currency)
  {
    return $this->showOne($currency);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Currency\CurrencyRequest  $request
   * @param  App\Http\Modules\Currency\Currency  $currency
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(CurrencyRequest $request, Currency $currency)
  {
    $currency->update($request->validated());

    return $this->showOne($currency);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Currency\Currency  $currency
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Currency $currency)
  {
    $currency->secureDelete();

    return $this->showOne($currency);
  }
}
