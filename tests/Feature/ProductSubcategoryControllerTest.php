<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Http\Modules\ProductSubcategory\ProductSubcategory;

class ProductSubcategoryControllerTest extends ApiTestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'ProductDepartmentSeeder', 'ProductCategorySeeder', 'ProductSubcategorySeeder']);
    }

    /**
    * @test
    */
    public function a_guest_cannot_access_to_product_subcategory_resources()
    {
        $this->getJson(route('product-subcategories.index'))->assertUnauthorized();
        $this->getJson(route('product-subcategories.show', rand()))->assertUnauthorized();
        $this->postJson(route('product-subcategories.store'))->assertUnauthorized();
        $this->putJson(route('product-subcategories.update', rand()))->assertUnauthorized();
        $this->deleteJson(route('product-subcategories.destroy', rand()))->assertUnauthorized();
    }

    /**
    * @test
    */
    public function an_user_without_permission_cannot_access_to_product_subcategory_resources()
    {
        $this->signIn();
        $randomProductSubcategoryId = ProductSubcategory::all()->random()->id;

        $this->getJson(route('product-subcategories.index'))->assertForbidden();
        $this->getJson(route('product-subcategories.show', $randomProductSubcategoryId))->assertForbidden();
        $this->postJson(route('product-subcategories.store'))->assertForbidden();
        $this->putJson(route('product-subcategories.update', $randomProductSubcategoryId))->assertForbidden();
        $this->deleteJson(route('product-subcategories.destroy', $randomProductSubcategoryId))->assertForbidden();
    }

    /**
    * @test
    */
    public function an_user_with_role_with_permission_can_see_all_product_subcategories()
    {

        $role = $this->getRoleWithPermissionsTo(['product-subcategories.index']);
        $user = $this->signInWithRole($role);

        $response = $this->getJson(route('product-subcategories.index'))
        ->assertOk();
        
        foreach (ProductSubcategory::limit(10)->get() as $productSubcategory) {
            $response->assertJsonFragment($productSubcategory->toArray());
        }
    }

    /**
    * @test
    */
    public function an_user_with_role_with_permission_can_see_a_product_subcategory()
    {
        $role = $this->getRoleWithPermissionsTo(['product-subcategories.show']);
        $user = $this->signInWithRole($role);

        $productSubcategory = factory(ProductSubcategory::class)->create();

        $this->getJson(route('product-subcategories.show', $productSubcategory->id))
        ->assertOk()
        ->assertJson($productSubcategory->toArray());
    }

    /**
    * @test
    */
    public function an_user_with_role_with_permission_can_store_a_product_sub_categories()
    {
        $role = $this->getRoleWithPermissionsTo(['product-subcategories.store']);
        $user = $this->signInWithRole($role);

        $attributes = factory(ProductSubcategory::class)->raw();

        $this->postJson(route('product-subcategories.store'), $attributes)
        ->assertCreated();
        
        $this->assertDatabaseHas('product_subcategories', $attributes);
    }

    /**
    * @test
    */
    public function an_user_with_role_with_permission_can_update_a_product_sub_categories()
    {
        $this->withExceptionHandling();

        $role = $this->getRoleWithPermissionsTo(['product-subcategories.update']);
        $user = $this->signInWithRole($role);

        $productSubcategory = factory(ProductSubcategory::class)->create();

        $attributes = factory(ProductSubcategory::class)->raw();

        $this->putJson(route('product-subcategories.update', $productSubcategory->id), $attributes)
        ->assertOk();

        $this->assertDatabaseHas('product_subcategories', $attributes);
    }

    /**
    * @test
    */
    public function an_user_with_role_with_permission_can_destroy_a_product_sub_categories()
    {
        $role = $this->getRoleWithPermissionsTo(['product-subcategories.destroy']);
        $user = $this->signInWithRole($role);

        $productSubcategory = factory(ProductSubcategory::class)->create();

        $this->deleteJson(route('product-subcategories.destroy', $productSubcategory->id))
        ->assertOk();

        $this->assertDatabaseMissing('product_subcategories', $productSubcategory->toArray());
    }
}
