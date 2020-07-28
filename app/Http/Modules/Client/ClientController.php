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
    $clients = Client::query();

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
    $client->update($request->validated());

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
    $client->secureDelete();

    return $this->showOne($client);
  }
}
