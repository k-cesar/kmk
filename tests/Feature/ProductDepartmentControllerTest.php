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
  public function a_guest_cannot_access_to_productDepartment_resources()
  {
    $this->getJson(route('productDepartments.index'))->assertUnauthorized();
    $this->getJson(route('productDepartments.show', rand()))->assertUnauthorized();
    $this->postJson(route('productDepartments.store'))->assertUnauthorized();
    $this->putJson(route('productDepartments.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('productDepartments.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_productDepartment_resources()
  {
    $this->signIn();
    
    $randomProductDepartmentId = ProductDepartment::all()->random()->id;

    $this->getJson(route('productDepartments.index'))->assertForbidden();
    $this->getJson(route('productDepartments.show', $randomProductDepartmentId))->assertForbidden();
    $this->postJson(route('productDepartments.store'))->assertForbidden();
    $this->putJson(route('productDepartments.update', $randomProductDepartmentId))->assertForbidden();
    $this->deleteJson(route('productDepartments.destroy', $randomProductDepartmentId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_productDepartments()
  {

    $role = $this->getRoleWithPermissionsTo(['productDepartments.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('productDepartments.index'))
      ->assertOk();
    
    foreach (ProductDepartment::limit(10)->get() as $productDepartment) {
      $response->assertJsonFragment($productDepartment->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_productDepartment()
  {
    $role = $this->getRoleWithPermissionsTo(['productDepartments.show']);
    $user = $this->signInWithRole($role);

    $productDepartment = factory(ProductDepartment::class)->create();

    $this->getJson(route('productDepartments.show', $productDepartment->id))
      ->assertOk()
      ->assertJson($productDepartment->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_productDepartment()
  {
    $role = $this->getRoleWithPermissionsTo(['productDepartments.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(ProductDepartment::class)->raw();

    $this->postJson(route('productDepartments.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('product_departments', $attributes);
  }


  /**
   * @test
   */
 /* public function an_user_with_role_with_permission_can_update_a_productDepartment()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['productDepartments.update']);
    $user = $this->signInWithRole($role);

    $productDepartment = factory(ProductDepartment::class)->create();

    $attributes = factory(ProductDepartment::class)->raw();

    $this->putJson(route('productDepartments.update', $productDepartment->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('product_departments', $attributes);
  }*/

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_productDepartment()
  {
    $role = $this->getRoleWithPermissionsTo(['productDepartments.destroy']);
    $user = $this->signInWithRole($role);

    $productDepartment = factory(ProductDepartment::class)->create();

    $this->deleteJson(route('productDepartments.destroy', $productDepartment->id))
      ->assertOk();

    $this->assertDatabaseMissing('product_departments', $productDepartment->toArray());
  }

}