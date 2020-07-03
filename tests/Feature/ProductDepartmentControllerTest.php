<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\ProductDepartment\ProductDepartment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProductDepartmentControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'ProductDepartmentSeeder']);
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
  public function an_user_with_role_with_permission_can_see_all_product_departments()
  {

    $role = $this->getRoleWithPermissionsTo(['product-departments.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('product-departments.index'))
      ->assertOk();
    
    foreach (ProductDepartment::limit(10)->get() as $productDepartment) {
      $response->assertJsonFragment($productDepartment->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_product_department()
  {
    $role = $this->getRoleWithPermissionsTo(['product-departments.show']);
    $user = $this->signInWithRole($role);

    $productDepartment = factory(ProductDepartment::class)->create();

    $this->getJson(route('product-departments.show', $productDepartment->id))
      ->assertOk()
      ->assertJson($productDepartment->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_product_department()
  {
    $role = $this->getRoleWithPermissionsTo(['product-departments.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(ProductDepartment::class)->raw();

    $this->postJson(route('product-departments.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('product_departments', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_product_department()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['product-departments.update']);
    $user = $this->signInWithRole($role);

    $productDepartment = factory(ProductDepartment::class)->create();

    $attributes = factory(ProductDepartment::class)->raw();

    $this->putJson(route('product-departments.update', $productDepartment->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('product_departments', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_product_department()
  {
    $role = $this->getRoleWithPermissionsTo(['product-departments.destroy']);
    $user = $this->signInWithRole($role);

    $productDepartment = factory(ProductDepartment::class)->create();

    $this->deleteJson(route('product-departments.destroy', $productDepartment->id))
      ->assertOk();

    $this->assertDatabaseMissing('product_departments', $productDepartment->toArray());
  }

}