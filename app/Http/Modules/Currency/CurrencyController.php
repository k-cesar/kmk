<?php

namespace App\Http\Modules\Currency;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
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
    $currencies = Currency::query();

    return $this->showAll($currencies, Schema::getColumnListing((new Currency)->getTable()));
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

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $currencies = Currency::select('id', 'name');

    return $this->showAll($currencies, Schema::getColumnListing((new Currency)->getTable()));
  }
}
