<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Provider\Provider;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProviderControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'ProviderSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_provider_resources()
  {
    $this->getJson(route('providers.index'))->assertUnauthorized();
    $this->getJson(route('providers.show', rand()))->assertUnauthorized();
    $this->postJson(route('providers.store'))->assertUnauthorized();
    $this->putJson(route('providers.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('providers.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_provider_resources()
  {
    $this->signIn();
    
    $randomProviderId = Provider::all()->random()->id;

    $this->getJson(route('providers.index'))->assertForbidden();
    $this->getJson(route('providers.show', $randomProviderId))->assertForbidden();
    $this->postJson(route('providers.store'))->assertForbidden();
    $this->putJson(route('providers.update', $randomProviderId))->assertForbidden();
    $this->deleteJson(route('providers.destroy', $randomProviderId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_providers()
  {

    $role = $this->getRoleWithPermissionsTo(['providers.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('providers.index'))
      ->assertOk();
    
    foreach (Provider::limit(10)->get() as $provider) {
      $response->assertJsonFragment($provider->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_provider()
  {
    $role = $this->getRoleWithPermissionsTo(['providers.show']);
    $user = $this->signInWithRole($role);

    $provider = factory(Provider::class)->create();

    $this->getJson(route('providers.show', $provider->id))
      ->assertOk()
      ->assertJson($provider->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_provider()
  {
    $role = $this->getRoleWithPermissionsTo(['providers.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(Provider::class)->raw();

    $this->postJson(route('providers.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('providers', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_provider()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['providers.update']);
    $user = $this->signInWithRole($role);

    $provider = factory(Provider::class)->create();

    $attributes = factory(Provider::class)->raw();

    $this->putJson(route('providers.update', $provider->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('providers', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_provider()
  {
    $role = $this->getRoleWithPermissionsTo(['providers.destroy']);
    $user = $this->signInWithRole($role);

    $provider = factory(Provider::class)->create();

    $this->deleteJson(route('providers.destroy', $provider->id))
      ->assertOk();

    $this->assertDatabaseMissing('providers', $provider->toArray());
  }

}