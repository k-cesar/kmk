<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\ProductSubCategories\ProductSubCategories;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProductSubCategoriesControllerTest extends ApiTestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'ProductDepartmentSeeder', 'ProductCategorySeeder', 'ProductSubcategoriesSeeder']);
    }

    public function a_guest_cannot_access_to_product_sub_category_resources()
    {
        $this->getJson(route('product-sub-categories.index'))->assertUnauthorized();
        $this->getJson(route('product-sub-categories.show', rand()))->assertUnauthorized();
        $this->postJson(route('product-sub-categories.store'))->assertUnauthorized();
        $this->putJson(route('product-sub-categories.update', rand()))->assertUnauthorized();
        $this->deleteJson(route('product-sub-categories.destroy', rand()))->assertUnauthorized();
    }

    public function an_user_without_permission_cannot_access_to_product_sub_category_resources()
    {
        $this->signIn();
        $randomProductSubCategoriesId = ProductSubCategories::all()->random()->id;

        $this->getJson(route('product-sub-categories.index'))->assertForbidden();
        $this->getJson(route('product-sub-categories.show', $randomProductSubCategoriesId))->assertForbidden();
        $this->postJson(route('product-sub-categories.store'))->assertForbidden();
        $this->putJson(route('product-sub-categories.update', $randomProductSubCategoriesId))->assertForbidden();
        $this->deleteJson(route('product-sub-categories.destroy', $randomProductSubCategoriesId))->assertForbidden();
    }

    public function an_user_with_role_with_permission_can_see_all_product_sub_categories()
    {

        $role = $this->getRoleWithPermissionsTo(['product-sub-categories.index']);
        $user = $this->signInWithRole($role);

        $response = $this->getJson(route('product-sub-categories.index'))
        ->assertOk();
        
        foreach (ProductSubCategories::limit(10)->get() as $productSubCategories) {
        $response->assertJsonFragment($productSubCategories->toArray());
        }
    }

    public function an_user_with_role_with_permission_can_see_a_product_sub_categories()
    {
        $role = $this->getRoleWithPermissionsTo(['product-sub-categories.show']);
        $user = $this->signInWithRole($role);

        $productSubCategories = factory(ProductSubCategories::class)->create();

        $this->getJson(route('product-sub-categories.show', $productSubCategories->id))
        ->assertOk()
        ->assertJson($productSubCategories->toArray());
    }

    public function an_user_with_role_with_permission_can_store_a_product_sub_categories()
    {
        $role = $this->getRoleWithPermissionsTo(['product-sub-categories.store']);
        $user = $this->signInWithRole($role);

        $attributes = factory(ProductSubCategories::class)->raw();

        $this->postJson(route('product-sub-categories.store'), $attributes)
        ->assertCreated();
        
        $this->assertDatabaseHas('product_subcategories', $attributes);
    }

    public function an_user_with_role_with_permission_can_update_a_product_sub_categories()
    {
        $this->withExceptionHandling();

        $role = $this->getRoleWithPermissionsTo(['product-sub-categories.update']);
        $user = $this->signInWithRole($role);

        $productSubCategories = factory(ProductSubCategories::class)->create();

        $attributes = factory(ProductSubCategories::class)->raw();

        $this->putJson(route('product-sub-categories.update', $productSubCategories->id), $attributes)
        ->assertOk();

        $this->assertDatabaseHas('product_subcategories', $attributes);
    }

    public function an_user_with_role_with_permission_can_destroy_a_product_sub_categories()
    {
        $role = $this->getRoleWithPermissionsTo(['product-sub-categories.destroy']);
        $user = $this->signInWithRole($role);

        $productSubCategories = factory(ProductSubCategories::class)->create();

        $this->deleteJson(route('product-sub-categories.destroy', $productSubCategories->id))
        ->assertOk();

        $this->assertDatabaseMissing('product_subcategories', $productSubCategories->toArray());
    }
}
