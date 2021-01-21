<?php

namespace App\Http\Modules\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\Provider\Provider;

class ProviderController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $providers = Provider::query();

    return $this->showAll($providers, Schema::getColumnListing((new Provider)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Provider\ProviderRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(ProviderRequest $request)
  {
    $provider = Provider::create($request->validated());

    return $this->showOne($provider, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Provider\Provider  $provider
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Provider $provider)
  {
    return $this->showOne($provider);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Provider\ProviderRequest  $request
   * @param  App\Http\Modules\Provider\Provider  $provider
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(ProviderRequest $request, Provider $provider)
  {
    $provider->update($request->validated());

    return $this->showOne($provider);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Provider\Provider  $provider
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Provider $provider)
  {
    $provider->secureDelete();

    return $this->showOne($provider);
  }

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $providers = Provider::select('id', 'name')
      ->withOut('country');

    return $this->showAll($providers, Schema::getColumnListing((new Provider)->getTable()));
  }
}
