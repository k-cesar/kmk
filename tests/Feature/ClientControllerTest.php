<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Client\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'ClientSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_client_resources()
  {
    $this->getJson(route('clients.index'))->assertUnauthorized();
    $this->getJson(route('clients.show', rand()))->assertUnauthorized();
    $this->postJson(route('clients.store'))->assertUnauthorized();
    $this->putJson(route('clients.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('clients.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_client_resources()
  {
    $this->signIn();
    
    $randomClientId = Client::all()->random()->id;

    $this->getJson(route('clients.index'))->assertForbidden();
    $this->getJson(route('clients.show', $randomClientId))->assertForbidden();
    $this->postJson(route('clients.store'))->assertForbidden();
    $this->putJson(route('clients.update', $randomClientId))->assertForbidden();
    $this->deleteJson(route('clients.destroy', $randomClientId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_clients()
  {
    $user = $this->signInWithPermissionsTo(['clients.index']);

    $response = $this->getJson(route('clients.index'))
      ->assertOk();
    
    foreach (Client::visible($user)->limit(10)->get() as $client) {
      $response->assertJsonFragment($client->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_client()
  {
    $this->signInWithPermissionsTo(['clients.show']);

    $client = factory(Client::class)->create();

    $this->getJson(route('clients.show', $client->id))
      ->assertOk()
      ->assertJson($client->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_client()
  {
    $user = $this->signInWithPermissionsTo(['clients.store']);

    $attributes = factory(Client::class)->raw(['country_id' => $user->company->country_id]);

    $extraAttributes = [
      'phone' => $user->phone,
      'email' => $user->email,
    ];

    $this->postJson(route('clients.store'), array_merge($attributes, $extraAttributes))
      ->assertCreated();
    
    $this->assertDatabaseHas('clients', $attributes);

    $this->assertDatabaseHas('company_clients', array_merge([
      'company_id' => $user->company_id
    ], $extraAttributes));
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_client()
  {
    $user = $this->signInWithPermissionsTo(['clients.update']);

    $client = factory(Client::class)->create(['country_id' => $user->company->country_id]);

    $client->companies()->syncWithoutDetaching([
      $user->company_id => [
        'email' => '',
        'phone' => '',
      ]
    ]);

    $attributes = factory(Client::class)->raw(['country_id' => $user->company->country_id]);

    $extraAttributes = [
      'phone' => $user->phone,
      'email' => $user->email,
    ];

    $this->putJson(route('clients.update', $client->id), array_merge($attributes, $extraAttributes))
      ->assertOk();

    if ($user->role->level < 2) {
      $this->assertDatabaseHas('clients', $attributes);
    }

    $this->assertDatabaseHas('company_clients', array_merge([
      'client_id' => $client->id,
      'company_id' => $user->company_id
    ], $extraAttributes));
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_client()
  {
    $user = $this->signInWithPermissionsTo(['clients.destroy']);

    $user->role->update(['level' => 1]);

    $client = factory(Client::class)->create();

    $this->deleteJson(route('clients.destroy', $client->id))
      ->assertOk();

    $this->assertDatabaseMissing('clients', $client->toArray());
  }

  

  /**
   * @test
   */
  public function an_user_can_see_all_clients_options()
  {
    $user = $this->signIn();

    $response = $this->getJson(route('clients.options'))
      ->assertOk();

    $clients = Client::select('id', 'name', 'nit','address')
      ->visible($user)
      ->limit(10)
      ->get();

    foreach ($clients as $client) {
      $response->assertJsonFragment($client->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_not_store_a_client_nit_with_symbols()
  {
    $user = $this->signInWithPermissionsTo(['clients.store']);

    $attributes = factory(Client::class)->raw(['country_id' => $user->company->country_id, 'nit' => '123.123']);

    $extraAttributes = [
      'phone' => $user->phone,
      'email' => $user->email,
    ];

    $this->postJson(route('clients.store'), array_merge($attributes, $extraAttributes))
      ->assertStatus(422)
      ->assertJsonValidationErrors('nit');
  }



}