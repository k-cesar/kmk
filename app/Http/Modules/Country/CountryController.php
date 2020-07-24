<?php

namespace App\Http\Modules\Country;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Modules\Country\Country;
use Illuminate\Support\Facades\Schema;

class CountryController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $countries = Country::query();

    return $this->showAll($countries, Schema::getColumnListing((new Country)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Country\CountryRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(CountryRequest $request)
  {
    $country = Country::create($request->validated());

    return $this->showOne($country, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Country\Country  $country
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Country $country)
  {
    return $this->showOne($country);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Country\CountryRequest  $request
   * @param  App\Http\Modules\Country\Country  $country
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(CountryRequest $request, Country $country)
  {
    $country->update($request->validated());

    return $this->showOne($country);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Country\Country  $country
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Country $country)
  {
    $country->secureDelete();

    return $this->showOne($country);
  }

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $countries = Country::select('id', 'name');

    return $this->showAll($countries, Schema::getColumnListing((new Country)->getTable()));
  }
}
