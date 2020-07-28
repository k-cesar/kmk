<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Client\Client;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ClientControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'ClientSeeder']);
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
  public function an_user_with_role_with_permission_can_see_all_clients()
  {

    $role = $this->getRoleWithPermissionsTo(['clients.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('clients.index'))
      ->assertOk();
    
    foreach (Client::limit(10)->get() as $client) {
      $response->assertJsonFragment($client->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_client()
  {
    $role = $this->getRoleWithPermissionsTo(['clients.show']);
    $user = $this->signInWithRole($role);

    $client = factory(Client::class)->create();

    $this->getJson(route('clients.show', $client->id))
      ->assertOk()
      ->assertJson($client->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_client()
  {
    $role = $this->getRoleWithPermissionsTo(['clients.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(Client::class)->raw();

    $this->postJson(route('clients.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('clients', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_client()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['clients.update']);
    $user = $this->signInWithRole($role);

    $client = factory(Client::class)->create();

    $attributes = factory(Client::class)->raw();

    $this->putJson(route('clients.update', $client->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('clients', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_client()
  {
    $role = $this->getRoleWithPermissionsTo(['clients.destroy']);
    $user = $this->signInWithRole($role);

    $client = factory(Client::class)->create();

    $this->deleteJson(route('clients.destroy', $client->id))
      ->assertOk();

    $this->assertDatabaseMissing('clients', $client->toArray());
  }

}