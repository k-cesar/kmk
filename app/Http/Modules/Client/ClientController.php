<?php

namespace App\Http\Modules\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class ClientController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $clients = Client::visible(auth()->user())
      ->with(['companies' => function ($query) {
        $query->where('id', auth()->user()->company_id);
      }]);

    return $this->showAll($clients, Schema::getColumnListing((new Client)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Client\ClientRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(ClientRequest $request)
  {
    $client = Client::create($request->validated());

    $client->companies()->syncWithoutDetaching([
      auth()->user()->company_id => [
        'email' => $request->validated()['email'],
        'phone' => $request->validated()['phone'],
      ]
    ]);

    return $this->showOne($client, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Client\Client  $client
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Client $client)
  {
    $this->authorize('manage', $client);

    $client->load(['companies' => function ($query) {
      $query->where('id', auth()->user()->company_id);
    }]);

    return $this->showOne($client);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Client\ClientRequest  $request
   * @param  App\Http\Modules\Client\Client  $client
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(ClientRequest $request, Client $client)
  {
    $this->authorize('manage', $client);

    $client->update($request->validated());

    $client->companies()->syncWithoutDetaching([
      auth()->user()->company_id => [
        'email' => $request->validated()['email'],
        'phone' => $request->validated()['phone'],
      ]
    ]);

    return $this->showOne($client);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Client\Client  $client
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Client $client)
  {
    $this->authorize('destroy', $client);

    if ($client->sells()->exists()) {
      return $this->errorResponse(409, 'El cliente ya ha participado en procesos de venta.');
    }

    $client->companies()->detach();
    
    $client->forceDelete();

    return $this->showOne($client);
  }

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $clients = Client::select('id', 'name', 'nit','address')
      ->visible(auth()->user())
      ->with(['companies' => function ($query) {
        $query->where('id', auth()->user()->company_id);
      }]);

    return $this->showAll($clients, Schema::getColumnListing((new Client)->getTable()));
  }

}
