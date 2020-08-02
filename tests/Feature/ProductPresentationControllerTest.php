<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\ProductPresentation\ProductPresentation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProductPresentationControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'ProductPresentationSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_product_presentation_resources()
  {
    $this->getJson(route('product-presentations.index'))->assertUnauthorized();
    $this->getJson(route('product-presentations.show', rand()))->assertUnauthorized();
    $this->postJson(route('product-presentations.store'))->assertUnauthorized();
    $this->putJson(route('product-presentations.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('product-presentations.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_product_presentation_resources()
  {
    $this->signIn();
    
    $randomProductPresentationId = ProductPresentation::all()->random()->id;

    $this->getJson(route('product-presentations.index'))->assertForbidden();
    $this->getJson(route('product-presentations.show', $randomProductPresentationId))->assertForbidden();
    $this->postJson(route('product-presentations.store'))->assertForbidden();
    $this->putJson(route('product-presentations.update', $randomProductPresentationId))->assertForbidden();
    $this->deleteJson(route('product-presentations.destroy', $randomProductPresentationId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_product_presentations()
  {

    $role = $this->getRoleWithPermissionsTo(['product-presentations.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('product-presentations.index'))
      ->assertOk();
    
    foreach (ProductPresentation::limit(10)->get() as $productPresentation) {
      $response->assertJsonFragment($productPresentation->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_product_presentation()
  {
    $role = $this->getRoleWithPermissionsTo(['product-presentations.show']);
    $user = $this->signInWithRole($role);

    $productPresentation = factory(ProductPresentation::class)->create();

    $this->getJson(route('product-presentations.show', $productPresentation->id))
      ->assertOk()
      ->assertJson($productPresentation->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_product_presentation()
  {
    $role = $this->getRoleWithPermissionsTo(['product-presentations.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(ProductPresentation::class)->raw();

    $this->postJson(route('product-presentations.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('product_presentations', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_product_presentation()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['product-presentations.update']);
    $user = $this->signInWithRole($role);

    $productPresentation = factory(ProductPresentation::class)->create();

    $attributes = factory(ProductPresentation::class)->raw();

    $this->putJson(route('product-presentations.update', $productPresentation->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('product_presentations', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_product_presentation()
  {
    $role = $this->getRoleWithPermissionsTo(['product-presentations.destroy']);
    $user = $this->signInWithRole($role);

    $productPresentation = factory(ProductPresentation::class)->create();

    $this->deleteJson(route('product-presentations.destroy', $productPresentation->id))
      ->assertOk();

    $this->assertDatabaseMissing('product_presentations', $productPresentation->toArray());
  }

}