<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Product\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProductControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'ProductSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_product_resources()
  {
    $this->getJson(route('products.index'))->assertUnauthorized();
    $this->getJson(route('products.show', rand()))->assertUnauthorized();
    $this->postJson(route('products.store'))->assertUnauthorized();
    $this->putJson(route('products.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('products.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_product_resources()
  {
    $this->signIn();
    
    $randomProductId = Product::all()->random()->id;

    $this->getJson(route('products.index'))->assertForbidden();
    $this->getJson(route('products.show', $randomProductId))->assertForbidden();
    $this->postJson(route('products.store'))->assertForbidden();
    $this->putJson(route('products.update', $randomProductId))->assertForbidden();
    $this->deleteJson(route('products.destroy', $randomProductId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_products()
  {

    $role = $this->getRoleWithPermissionsTo(['products.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('products.index'))
      ->assertOk();
    
    foreach (Product::limit(10)->get() as $product) {
      $response->assertJsonFragment($product->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_product()
  {
    $role = $this->getRoleWithPermissionsTo(['products.show']);
    $user = $this->signInWithRole($role);

    $product = factory(Product::class)->create();

    $this->getJson(route('products.show', $product->id))
      ->assertOk()
      ->assertJson($product->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_product()
  {
    $role = $this->getRoleWithPermissionsTo(['products.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(Product::class)->raw();
    $extraAttributes['country'] = factory(Product::class, 2)->create()->pluck('id')->toArray();

    $this->postJson(route('products.store'), array_merge($attributes, $extraAttributes))
      ->assertCreated();
    
    $this->assertDatabaseHas('products', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_product()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['products.update']);
    $user = $this->signInWithRole($role);

    $product = factory(Product::class)->create();

    $attributes = factory(Product::class)->raw();
    $extraAttributes['country'] = factory(Product::class, 2)->create()->pluck('id')->toArray();


    $this->putJson(route('products.update', $product->id), array_merge($attributes, $extraAttributes))
      ->assertOk();

    $this->assertDatabaseHas('products', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_product()
  {
    $role = $this->getRoleWithPermissionsTo(['products.destroy']);
    $user = $this->signInWithRole($role);

    $product = factory(Product::class)->create();

    $this->deleteJson(route('products.destroy', $product->id))
      ->assertOk();

    $this->assertDatabaseMissing('products', $product->toArray());
  }

}