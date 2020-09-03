<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\StoreFlag\StoreFlag;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreFlagControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'StoreFlagSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_store_flag_resources()
  {
    $this->getJson(route('store-flags.index'))->assertUnauthorized();
    $this->getJson(route('store-flags.show', rand()))->assertUnauthorized();
    $this->postJson(route('store-flags.store'))->assertUnauthorized();
    $this->putJson(route('store-flags.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('store-flags.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_store_flag_resources()
  {
    $this->signIn();
    
    $randomStoreFlagId = StoreFlag::all()->random()->id;

    $this->getJson(route('store-flags.index'))->assertForbidden();
    $this->getJson(route('store-flags.show', $randomStoreFlagId))->assertForbidden();
    $this->postJson(route('store-flags.store'))->assertForbidden();
    $this->putJson(route('store-flags.update', $randomStoreFlagId))->assertForbidden();
    $this->deleteJson(route('store-flags.destroy', $randomStoreFlagId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_store_flags()
  {
    $this->signInWithPermissionsTo(['store-flags.index']);

    $response = $this->getJson(route('store-flags.index'))
      ->assertOk();
    
    foreach (StoreFlag::limit(10)->get() as $storeFlag) {
      $response->assertJsonFragment($storeFlag->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_store_flag()
  {
    $this->signInWithPermissionsTo(['store-flags.show']);

    $storeFlag = factory(StoreFlag::class)->create();

    $this->getJson(route('store-flags.show', $storeFlag->id))
      ->assertOk()
      ->assertJson($storeFlag->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_store_flag()
  {
    $this->signInWithPermissionsTo(['store-flags.store']);

    $attributes = factory(StoreFlag::class)->raw();

    $this->postJson(route('store-flags.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('store_flags', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_store_flag()
  {
    $this->signInWithPermissionsTo(['store-flags.update']);

    $storeFlag = factory(StoreFlag::class)->create();

    $attributes = factory(StoreFlag::class)->raw();

    $this->putJson(route('store-flags.update', $storeFlag->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('store_flags', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_store_flag()
  {
    $this->signInWithPermissionsTo(['store-flags.destroy']);

    $storeFlag = factory(StoreFlag::class)->create();

    $this->deleteJson(route('store-flags.destroy', $storeFlag->id))
      ->assertOk();

    $this->assertDatabaseMissing('store_flags', $storeFlag->toArray());
  }

}