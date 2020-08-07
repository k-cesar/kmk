<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\StoreFormat\StoreFormat;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StoreFormatControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'StoreFormatSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_store_format_resources()
  {
    $this->getJson(route('store-formats.index'))->assertUnauthorized();
    $this->getJson(route('store-formats.show', rand()))->assertUnauthorized();
    $this->postJson(route('store-formats.store'))->assertUnauthorized();
    $this->putJson(route('store-formats.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('store-formats.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_store_format_resources()
  {
    $this->signIn();
    
    $randomStoreFormatId = StoreFormat::all()->random()->id;

    $this->getJson(route('store-formats.index'))->assertForbidden();
    $this->getJson(route('store-formats.show', $randomStoreFormatId))->assertForbidden();
    $this->postJson(route('store-formats.store'))->assertForbidden();
    $this->putJson(route('store-formats.update', $randomStoreFormatId))->assertForbidden();
    $this->deleteJson(route('store-formats.destroy', $randomStoreFormatId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_store_formats()
  {
    $this->signInWithPermissionsTo(['store-formats.index']);

    $response = $this->getJson(route('store-formats.index'))
      ->assertOk();
    
    foreach (StoreFormat::limit(10)->get() as $storeFormat) {
      $response->assertJsonFragment($storeFormat->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_store_format()
  {
    $this->signInWithPermissionsTo(['store-formats.show']);

    $storeFormat = factory(StoreFormat::class)->create();

    $this->getJson(route('store-formats.show', $storeFormat->id))
      ->assertOk()
      ->assertJson($storeFormat->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_store_format()
  {
    $this->signInWithPermissionsTo(['store-formats.store']);

    $attributes = factory(StoreFormat::class)->raw();

    $this->postJson(route('store-formats.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('store_formats', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_store_format()
  {
    $this->signInWithPermissionsTo(['store-formats.update']);

    $storeFormat = factory(StoreFormat::class)->create();

    $attributes = factory(StoreFormat::class)->raw();

    $this->putJson(route('store-formats.update', $storeFormat->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('store_formats', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_store_format()
  {
    $this->signInWithPermissionsTo(['store-formats.destroy']);

    $storeFormat = factory(StoreFormat::class)->create();

    $this->deleteJson(route('store-formats.destroy', $storeFormat->id))
      ->assertOk();

    $this->assertDatabaseMissing('store_formats', $storeFormat->toArray());
  }

}