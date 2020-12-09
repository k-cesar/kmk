<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\ProductCategory\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductCategoryControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'ProductDepartmentSeeder', 'ProductCategorySeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_product_category_resources()
  {
    $this->getJson(route('product-categories.index'))->assertUnauthorized();
    $this->getJson(route('product-categories.show', rand()))->assertUnauthorized();
    $this->postJson(route('product-categories.store'))->assertUnauthorized();
    $this->putJson(route('product-categories.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('product-categories.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_product_category_resources()
  {
    $this->signIn();
    
    $randomProductCategoryId = ProductCategory::all()->random()->id;

    $this->getJson(route('product-categories.index'))->assertForbidden();
    $this->getJson(route('product-categories.show', $randomProductCategoryId))->assertForbidden();
    $this->postJson(route('product-categories.store'))->assertForbidden();
    $this->putJson(route('product-categories.update', $randomProductCategoryId))->assertForbidden();
    $this->deleteJson(route('product-categories.destroy', $randomProductCategoryId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_product_categories()
  {
    $this->signInWithPermissionsTo(['product-categories.index']);

    $response = $this->getJson(route('product-categories.index'))
      ->assertOk();
    
    foreach (ProductCategory::limit(10)->get() as $productCategory) {
      $response->assertJsonFragment($productCategory->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_product_category()
  {
    $this->signInWithPermissionsTo(['product-categories.show']);

    $productCategory = factory(ProductCategory::class)->create();

    $this->getJson(route('product-categories.show', $productCategory->id))
      ->assertOk()
      ->assertJson($productCategory->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_product_category()
  {
    $this->signInWithPermissionsTo(['product-categories.store']);

    $attributes = factory(ProductCategory::class)->raw();

    $this->postJson(route('product-categories.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('product_categories', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_product_category()
  {
    $this->signInWithPermissionsTo(['product-categories.update']);

    $productCategory = factory(ProductCategory::class)->create();

    $attributes = factory(ProductCategory::class)->raw();

    $this->putJson(route('product-categories.update', $productCategory->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('product_categories', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_product_category()
  {
    $this->signInWithPermissionsTo(['product-categories.destroy']);

    $productCategory = factory(ProductCategory::class)->create();

    $this->deleteJson(route('product-categories.destroy', $productCategory->id))
      ->assertOk();

    $this->assertDatabaseMissing('product_categories', $productCategory->toArray());
  }

  /**
   * @test
   */
  public function an_user_can_see_all_product_categories_options()
  {
    $user = $this->signIn();

    $response = $this->getJson(route('product-categories.options'))
      ->assertOk();

    $productCategories = ProductCategory::select(['id', 'name'])
      ->withOut('productDepartment')
      ->limit(10)
      ->get();

    foreach ($productCategories as $productCategory) {
      $response->assertJsonFragment($productCategory->toArray());
    }
  }

}