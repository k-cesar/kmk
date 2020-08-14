<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\StockCountsDetail\StockCountsDetail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StockCountsDetailControllerTest extends ApiTestCase
{
    use DatabaseMigrations, RefreshDatabase;

    /**
   * @test
   */
    public function setUp(): void
    {
        parent::setUp();
        $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'StockCountsDetailSeeder']);

    }

    /**
   * @test
   */
    public function a_guest_cannot_access_to_stock_counts_detail_resources()
    {
        $this->getJson(route('stock-counts-detail.index'))->assertUnauthorized();
        $this->getJson(route('stock-counts-detail.show', rand()))->assertUnauthorized();
        $this->postJson(route('stock-counts-detail.store'))->assertUnauthorized();
        $this->putJson(route('stock-counts-detail.update', rand()))->assertUnauthorized();
        $this->deleteJson(route('stock-counts-detail.destroy', rand()))->assertUnauthorized();
    }

    /**
   * @test
   */
    public function an_user_without_permission_cannot_access_to_stock_counts_detail_resources()
    {
        $this->signIn();
        
        $randomStockCountsDetailID = StockCountsDetail::all()->random()->id;

        $this->getJson(route('stock-counts-detail.index'))->assertForbidden();
        $this->getJson(route('stock-counts-detail.show', $randomStockCountsDetailID))->assertForbidden();
        $this->postJson(route('stock-counts-detail.store'))->assertForbidden();
        $this->putJson(route('stock-counts-detail.update', $randomStockCountsDetailID))->assertForbidden();
        $this->deleteJson(route('stock-counts-detail.destroy', $randomStockCountsDetailID))->assertForbidden();
    }

    /**
   * @test
   */
    public function an_user_with_permission_can_see_all_stock_counts_detail()
    {
        $this->signInWithPermissionsTo(['stock-counts-detail.index']);

        $response = $this->getJson(route('stock-counts-detail.index'))
        ->assertOk();
        
        foreach (StockCountsDetail::limit(10)->get() as $stockCountDetail) {
            $response->assertJsonFragment($stockCountDetail->toArray());
        }
    }

    /**
   * @test
   */
    public function an_user_with_permission_can_see_a_stock_counts_detail()
    {
        $this->signInWithPermissionsTo(['stock-counts-detail.show']);

        $stockCountDetail = factory(StockCountsDetail::class)->create();

        $this->getJson(route('stock-counts-detail.show', $stockCountDetail->id))
        ->assertOk()
        ->assertJson($stockCountDetail->toArray());
    }

    /**
   * @test
   */
    public function an_user_with_permission_can_store_a_stock_counts_detail()
    {
        $this->signInWithPermissionsTo(['stock-counts-detail.store']);

        $attributes = factory(StockCountsDetail::class)->raw();

        $this->postJson(route('stock-counts-detail.store'), $attributes)
        ->assertCreated();
        $this->assertDatabaseHas('stock_counts_detail', $attributes);
    }

    /**
   * @test
   */
    public function an_user_with_permission_can_update_a_stock_counts_detail()
    {
        $this->signInWithPermissionsTo(['stock-counts-detail.update']);

        $stockCountDetail = factory(StockCountsDetail::class)->create();

        $attributes = factory(StockCountsDetail::class)->raw();

        $this->putJson(route('stock-counts-detail.update', $stockCountDetail->id), $attributes)
        ->assertOk();

        $this->assertDatabaseHas('stock_counts_detail', $attributes);
    }

    /**
   * @test
   */
    public function an_user_with_permission_can_destroy_a_stock_counts_detail()
    {
        $this->signInWithPermissionsTo(['stock-counts-detail.destroy']);

        $stockCountDetail = factory(StockCountsDetail::class)->create();

        $this->deleteJson(route('stock-counts-detail.destroy', $stockCountDetail->id))
        ->assertOk();

        $this->assertDatabaseMissing('stock_counts_detail', $stockCountDetail->toArray());
    }
}
