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
    $this->signInWithPermissionsTo(['clients.index']);

    $response = $this->getJson(route('clients.index'))
      ->assertOk();
    
    foreach (Client::limit(10)->get() as $client) {
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

    $attributes = factory(Client::class)->raw();

    $extraAttributes = [
      'phone' => $user->phone,
      'email' => $user->email,
    ];

    $this->postJson(route('clients.store'), array_merge($attributes, $extraAttributes))
      ->assertCreated();
    
    $this->assertDatabaseHas('clients', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_client()
  {
    $user = $this->signInWithPermissionsTo(['clients.update']);

    $client = factory(Client::class)->create();

    $attributes = factory(Client::class)->raw();

    $extraAttributes = [
      'phone' => $user->phone,
      'email' => $user->email,
    ];

    $this->putJson(route('clients.update', $client->id), array_merge($attributes, $extraAttributes))
      ->assertOk();

    $this->assertDatabaseHas('clients', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_client()
  {
    $this->signInWithPermissionsTo(['clients.destroy']);

    $client = factory(Client::class)->create();

    $this->deleteJson(route('clients.destroy', $client->id))
      ->assertOk();

    $this->assertDatabaseMissing('clients', $client->toArray());
  }
}