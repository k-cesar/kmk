<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Store\Store;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StoreAdjustmentControllerTest extends ApiTestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'StoreSeeder', 'ProductSeeder', 'PurchaseSeeder']);
    }

    /**
     * @test
     */
    public function a_guest_cannot_access_to_store_adjustment_resources()
    {
        $this->getJson(route('store-adjustment.index'))->assertUnauthorized();
        $this->getJson(route('store-adjustment.show', rand()))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function an_user_without_permission_cannot_access_to_store_adjustment_resources()
    {
        $this->signIn();

        $randomStore = Store::all()->random()->id;

        $this->getJson(route('store-adjustment.index'))->assertForbidden();
        $this->getJson(route('store-adjustment.show', $randomStore))->assertForbidden();
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_all_store_adjustment()
    {
        $this->signInWithPermissionsTo(['store-adjustment.index']);

        $store = Store::has('products')->first();

        $this->getJson(route('store-adjustment.index', ['store_id' => $store->id]))
        ->assertOk();
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_a_store_adjustment()
    {
        $this->signInWithPermissionsTo(['store-adjustment.show']);

        $store = factory(Store::class)->create();

        $this->getJson(route('store-adjustment.show', $store->id))
        ->assertOk();
    }
}
