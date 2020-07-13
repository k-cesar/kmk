<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\StoreType\StoreType;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StoreTypeControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'StoreTypeSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_store_type_resources()
  {
    $this->getJson(route('store-types.index'))->assertUnauthorized();
    $this->getJson(route('store-types.show', rand()))->assertUnauthorized();
    $this->postJson(route('store-types.store'))->assertUnauthorized();
    $this->putJson(route('store-types.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('store-types.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_store_type_resources()
  {
    $this->signIn();
    
    $randomStoreTypeId = StoreType::all()->random()->id;

    $this->getJson(route('store-types.index'))->assertForbidden();
    $this->getJson(route('store-types.show', $randomStoreTypeId))->assertForbidden();
    $this->postJson(route('store-types.store'))->assertForbidden();
    $this->putJson(route('store-types.update', $randomStoreTypeId))->assertForbidden();
    $this->deleteJson(route('store-types.destroy', $randomStoreTypeId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_store_types()
  {

    $role = $this->getRoleWithPermissionsTo(['store-types.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('store-types.index'))
      ->assertOk();
    
    foreach (StoreType::limit(10)->get() as $storeType) {
      $response->assertJsonFragment($storeType->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_store_type()
  {
    $role = $this->getRoleWithPermissionsTo(['store-types.show']);
    $user = $this->signInWithRole($role);

    $storeType = factory(StoreType::class)->create();

    $this->getJson(route('store-types.show', $storeType->id))
      ->assertOk()
      ->assertJson($storeType->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_store_type()
  {
    $role = $this->getRoleWithPermissionsTo(['store-types.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(StoreType::class)->raw();

    $this->postJson(route('store-types.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('store_types', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_store_type()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['store-types.update']);
    $user = $this->signInWithRole($role);

    $storeType = factory(StoreType::class)->create();

    $attributes = factory(StoreType::class)->raw();

    $this->putJson(route('store-types.update', $storeType->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('store_types', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_store_type()
  {
    $role = $this->getRoleWithPermissionsTo(['store-types.destroy']);
    $user = $this->signInWithRole($role);

    $storeType = factory(StoreType::class)->create();

    $this->deleteJson(route('store-types.destroy', $storeType->id))
      ->assertOk();

    $this->assertDatabaseMissing('store_types', $storeType->toArray());
  }

}