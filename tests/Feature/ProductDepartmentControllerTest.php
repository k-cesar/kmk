<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Modules\ProductDepartment\ProductDepartment;

class ProductDepartmentControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'ProductDepartmentSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_product_department_resources()
  {
    $this->getJson(route('product-departments.index'))->assertUnauthorized();
    $this->getJson(route('product-departments.show', rand()))->assertUnauthorized();
    $this->postJson(route('product-departments.store'))->assertUnauthorized();
    $this->putJson(route('product-departments.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('product-departments.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_product_department_resources()
  {
    $this->signIn();
    
    $randomProductDepartmentId = ProductDepartment::all()->random()->id;

    $this->getJson(route('product-departments.index'))->assertForbidden();
    $this->getJson(route('product-departments.show', $randomProductDepartmentId))->assertForbidden();
    $this->postJson(route('product-departments.store'))->assertForbidden();
    $this->putJson(route('product-departments.update', $randomProductDepartmentId))->assertForbidden();
    $this->deleteJson(route('product-departments.destroy', $randomProductDepartmentId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_product_departments()
  {
    $this->signInWithPermissionsTo(['product-departments.index']);

    $response = $this->getJson(route('product-departments.index'))
      ->assertOk();
    
    foreach (ProductDepartment::limit(10)->get() as $productDepartment) {
      $response->assertJsonFragment($productDepartment->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_product_department()
  {
    $this->signInWithPermissionsTo(['product-departments.show']);

    $productDepartment = factory(ProductDepartment::class)->create();

    $this->getJson(route('product-departments.show', $productDepartment->id))
      ->assertOk()
      ->assertJson($productDepartment->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_product_department()
  {
    $this->signInWithPermissionsTo(['product-departments.store']);

    $attributes = factory(ProductDepartment::class)->raw();

    $this->postJson(route('product-departments.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('product_departments', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_product_department()
  {
    $this->signInWithPermissionsTo(['product-departments.update']);

    $productDepartment = factory(ProductDepartment::class)->create();

    $attributes = factory(ProductDepartment::class)->raw();

    $this->putJson(route('product-departments.update', $productDepartment->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('product_departments', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_product_department()
  {
    $this->signInWithPermissionsTo(['product-departments.destroy']);

    $productDepartment = factory(ProductDepartment::class)->create();

    $this->deleteJson(route('product-departments.destroy', $productDepartment->id))
      ->assertOk();

    $this->assertDatabaseMissing('product_departments', $productDepartment->toArray());
  }

  /**
   * @test
   */
  public function an_user_can_see_all_product_departments_options()
  {
    $user = $this->signIn();

    $response = $this->getJson(route('product-departments.options'))
      ->assertOk();

    $productDepartments = ProductDepartment::select(['id', 'name'])
      ->withOut('productDepartment')
      ->limit(10)
      ->get();

    foreach ($productDepartments as $productDepartment) {
      $response->assertJsonFragment($productDepartment->toArray());
    }
  }
}