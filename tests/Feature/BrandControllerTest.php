<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Brand\Brand;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BrandControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'BrandSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_brand_resources()
  {
    $this->getJson(route('brands.index'))->assertUnauthorized();
    $this->getJson(route('brands.show', rand()))->assertUnauthorized();
    $this->postJson(route('brands.store'))->assertUnauthorized();
    $this->putJson(route('brands.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('brands.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_brand_resources()
  {
    $this->signIn();
    
    $randomBrandId = Brand::all()->random()->id;

    $this->getJson(route('brands.index'))->assertForbidden();
    $this->getJson(route('brands.show', $randomBrandId))->assertForbidden();
    $this->postJson(route('brands.store'))->assertForbidden();
    $this->putJson(route('brands.update', $randomBrandId))->assertForbidden();
    $this->deleteJson(route('brands.destroy', $randomBrandId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_brands()
  {
    $this->signInWithPermissionsTo(['brands.index']);

    $response = $this->getJson(route('brands.index'))
      ->assertOk();
    
    foreach (Brand::limit(10)->get() as $brand) {
      $response->assertJsonFragment($brand->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_brand()
  {
    $this->signInWithPermissionsTo(['brands.show']);

    $brand = factory(Brand::class)->create();

    $this->getJson(route('brands.show', $brand->id))
      ->assertOk()
      ->assertJson($brand->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_brand()
  {
    $this->signInWithPermissionsTo(['brands.store']);

    $attributes = factory(Brand::class)->raw();

    $this->postJson(route('brands.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('brands', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_brand()
  {
    $this->signInWithPermissionsTo(['brands.update']);

    $brand = factory(Brand::class)->create();

    $attributes = factory(Brand::class)->raw();

    $this->putJson(route('brands.update', $brand->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('brands', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_brand()
  {
    $this->signInWithPermissionsTo(['brands.destroy']);

    $brand = factory(Brand::class)->create();

    $this->deleteJson(route('brands.destroy', $brand->id))
      ->assertOk();

    $this->assertDatabaseMissing('brands', $brand->toArray());
  }

}