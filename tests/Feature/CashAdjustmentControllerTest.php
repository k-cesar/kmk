<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Store\Store;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Modules\CashAdjustment\CashAdjustment;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CashAdjustmentControllerTest extends ApiTestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(['PermissionSeeder', 'CashAdjustmentSeeder']);
    }

    /**
     * @test
     */
    public function a_guest_cannot_access_to_cash_adjustment_resources()
    {
        $this->postJson(route('cash-adjustments.store'))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function an_user_without_permission_cannot_access_to_cash_adjustment_resources()
    {
        $this->signIn();

        $this->postJson(route('cash-adjustments.store'))->assertForbidden();
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_store_a_cash_adjustment()
    {
        $user = $this->signInWithPermissionsTo(['cash-adjustments.store']);

        $store = factory(Store::class)->create();

        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                $user->update(['company_id' => $store->company_id]);
            } else {
                $user->stores()->sync($store->id);
            }
        }

        $attributes = factory(CashAdjustment::class)->raw([
            'store_id'          => $store->id,
        ]);

        $this->postJson(route('cash-adjustments.store'), $attributes)
            ->assertCreated();

        $this->assertDatabaseHas('cash_adjustments', $attributes);
    }

    
}
