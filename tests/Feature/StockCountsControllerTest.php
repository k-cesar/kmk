<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\StockCounts\StockCounts;
use App\Http\Modules\Product\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StockCountsControllerTest extends ApiTestCase
{
    use DatabaseMigrations, RefreshDatabase;

    /**
   * @test
   */
    public function setUp(): void
    {
        parent::setUp();
        $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'StockCountsSeeder']);
    }

    /**
   * @test
   */
    public function a_guest_cannot_access_to_stock_counts_resources()
    {
        $this->getJson(route('stock-counts.index'))->assertUnauthorized();
        $this->getJson(route('stock-counts.show', rand()))->assertUnauthorized();
        $this->postJson(route('stock-counts.store'))->assertUnauthorized();
        $this->putJson(route('stock-counts.update', rand()))->assertUnauthorized();
        $this->deleteJson(route('stock-counts.destroy', rand()))->assertUnauthorized();
    }

    /**
   * @test
   */
    public function an_user_without_permission_cannot_access_to_stock_counts_resources()
    {
        $this->signIn();
        
        $randomStockCountsID = StockCounts::all()->random()->id;

        $this->getJson(route('stock-counts.index'))->assertForbidden();
        $this->getJson(route('stock-counts.show', $randomStockCountsID))->assertForbidden();
        $this->postJson(route('stock-counts.store'))->assertForbidden();
        $this->putJson(route('stock-counts.update', $randomStockCountsID))->assertForbidden();
        $this->deleteJson(route('stock-counts.destroy', $randomStockCountsID))->assertForbidden();
    }

    /**
   * @test
   */
    public function an_user_with_permission_can_see_all_stock_counts()
    {
        $this->signInWithPermissionsTo(['stock-counts.index']);

        $response = $this->getJson(route('stock-counts.index'))
        ->assertOk();
        
        foreach (StockCounts::limit(10)->get() as $stockCount) {
            $response->assertJsonFragment($stockCount->toArray());
        }
    }

    /**
   * @test
   */
    public function an_user_with_permission_can_see_a_stock_counts()
    {
        $this->signInWithPermissionsTo(['stock-counts.show']);

        $stockCount = factory(StockCounts::class)->create();

        $this->getJson(route('stock-counts.show', $stockCount->id))
        ->assertOk();
    }

    /**
   * @test
   */
    public function an_user_with_permission_can_store_a_stock_counts()
    {
        $this->signInWithPermissionsTo(['stock-counts.store']);

        $attributes = factory(StockCounts::class)->raw();

        $this->postJson(route('stock-counts.store'), $attributes)
        ->assertCreated();
        
        $this->assertDatabaseHas('stock_counts', $attributes);
    }

    /**
   * @test
   */
    public function an_user_with_permission_can_update_a_stock_counts()
    {
        $this->signInWithPermissionsTo(['stock-counts.update']);

        $stockCount = factory(StockCounts::class)->create();

        $attributes = factory(StockCounts::class)->raw();

        $this->putJson(route('stock-counts.update', $stockCount->id), $attributes)
        ->assertOk();

        $this->assertDatabaseHas('stock_counts', $attributes);
    }

    /**
   * @test
   */
    public function an_user_with_permission_can_destroy_a_stock_counts()
    {
        $this->signInWithPermissionsTo(['stock-counts.destroy']);

        $stockCount = factory(StockCounts::class)->create();

        $this->deleteJson(route('stock-counts.destroy', $stockCount->id))
        ->assertOk();

        $this->assertDatabaseMissing('stock_counts', $stockCount->toArray());
    }
}
