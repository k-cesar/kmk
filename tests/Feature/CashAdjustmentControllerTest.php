<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\CashAdjustment\CashAdjustment;
use App\Http\Modules\Store\Store;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CashAdjustmentControllerTest extends ApiTestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'StoreSeeder', 'StoreTurnSeeder', 'CashAdjustmentSeeder']);
    }

    /**
     * @test
     */
    public function a_guest_cannot_access_to_cash_adjustment_resources()
    {
        $this->getJson(route('cash-adjustment.index'))->assertUnauthorized();
        $this->getJson(route('cash-adjustment.show', rand()))->assertUnauthorized();
        $this->getJson(route('cash-adjustment.store'))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function an_user_without_permission_cannot_access_to_cash_adjustment_resources()
    {
        $this->signIn();

        $randomCashAdjustment = CashAdjustment::all()->random()->id;

        $this->getJson(route('cash-adjustment.index'))->assertForbidden();
        $this->getJson(route('cash-adjustment.show', $randomCashAdjustment))->assertForbidden();
        $this->getJson(route('cash-adjustment.store'))->assertForbidden();
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_all_cash_adjustment()
    {
        $this->signInWithPermissionsTo(['cash-adjustment.index']);

        $response = $this->getJson(route('cash-adjustment.index'))
            ->assertOk();
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_a_cash_adjustment()
    {
        $this->signInWithPermissionsTo(['cash-adjustment.show']);

        $store = factory(Store::class)->create();

        $this->getJson(route('cash-adjustment.show', $store->id))
        ->assertOk();
    }
}
