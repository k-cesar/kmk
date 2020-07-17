<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\StoreChain\StoreChain;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StoreChainControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'StoreChainSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_store_chain_resources()
  {
    $this->getJson(route('store-chains.index'))->assertUnauthorized();
    $this->getJson(route('store-chains.show', rand()))->assertUnauthorized();
    $this->postJson(route('store-chains.store'))->assertUnauthorized();
    $this->putJson(route('store-chains.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('store-chains.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_store_chain_resources()
  {
    $this->signIn();
    
    $randomStoreChainId = StoreChain::all()->random()->id;

    $this->getJson(route('store-chains.index'))->assertForbidden();
    $this->getJson(route('store-chains.show', $randomStoreChainId))->assertForbidden();
    $this->postJson(route('store-chains.store'))->assertForbidden();
    $this->putJson(route('store-chains.update', $randomStoreChainId))->assertForbidden();
    $this->deleteJson(route('store-chains.destroy', $randomStoreChainId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_store_chains()
  {

    $role = $this->getRoleWithPermissionsTo(['store-chains.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('store-chains.index'))
      ->assertOk();
    
    foreach (StoreChain::limit(10)->get() as $storeChain) {
      $response->assertJsonFragment($storeChain->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_store_chain()
  {
    $role = $this->getRoleWithPermissionsTo(['store-chains.show']);
    $user = $this->signInWithRole($role);

    $storeChain = factory(StoreChain::class)->create();

    $this->getJson(route('store-chains.show', $storeChain->id))
      ->assertOk()
      ->assertJson($storeChain->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_store_chain()
  {
    $role = $this->getRoleWithPermissionsTo(['store-chains.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(StoreChain::class)->raw();

    $this->postJson(route('store-chains.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('store_chains', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_store_chain()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['store-chains.update']);
    $user = $this->signInWithRole($role);

    $storeChain = factory(StoreChain::class)->create();

    $attributes = factory(StoreChain::class)->raw();

    $this->putJson(route('store-chains.update', $storeChain->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('store_chains', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_store_chain()
  {
    $role = $this->getRoleWithPermissionsTo(['store-chains.destroy']);
    $user = $this->signInWithRole($role);

    $storeChain = factory(StoreChain::class)->create();

    $this->deleteJson(route('store-chains.destroy', $storeChain->id))
      ->assertOk();

    $this->assertDatabaseMissing('store_chains', $storeChain->toArray());
  }

}