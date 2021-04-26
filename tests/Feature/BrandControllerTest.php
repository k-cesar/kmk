<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Brand\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BrandControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'BrandSeeder']);
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
    $user = $this->signInWithPermissionsTo(['brands.index']);

    $response = $this->getJson(route('brands.index'))
      ->assertOk();
    
    foreach (Brand::whereHasCompanyVisible($user)->limit(10)->get() as $brand) {
      $response->assertJsonFragment($brand->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_brand()
  {
    $user = $this->signInWithPermissionsTo(['brands.show']);

    $brand = factory(Brand::class)->create(['company_id' => $user->company_id]);

    $this->getJson(route('brands.show', $brand->id))
      ->assertOk()
      ->assertJson($brand->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_brand()
  {
    $user = $this->signInWithPermissionsTo(['brands.store']);

    $attributes = factory(Brand::class)->raw(['company_id' => $user->company_id]);

    $this->postJson(route('brands.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('brands', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_brand()
  {
    $user = $this->signInWithPermissionsTo(['brands.update']);

    $brand = factory(Brand::class)->create(['company_id' => $user->company_id]);

    $attributes = factory(Brand::class)->raw(['company_id' => $user->company_id]);

    $this->putJson(route('brands.update', $brand->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('brands', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_brand()
  {
    $user = $this->signInWithPermissionsTo(['brands.destroy']);

    $brand = factory(Brand::class)->create(['company_id' => $user->company_id]);

    $this->deleteJson(route('brands.destroy', $brand->id))
      ->assertOk();

    $this->assertDatabaseMissing('brands', $brand->toArray());
  }

  /**
   * @test
   */
  public function an_user_can_see_all_brands_options()
  {
    $user = $this->signIn();

    $response = $this->getJson(route('brands.options'))
      ->assertOk();

    $brands = Brand::select(['id', 'name'])
      ->visibleThroughCompany($user)
      ->withOut('maker')
      ->limit(10)
      ->get();

    foreach ($brands as $brand) {
      $response->assertJsonFragment($brand->toArray());
    }
  }

}