<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\StoreTurn\StoreTurn;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StoreTurnControllerTest extends ApiTestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(['PermissionSeeder', 'StoreTurnSeeder']);
    }

    /**
     * @test
     */
    public function a_guest_cannot_access_to_store_turn_resources()
    {
        $this->getJson(route('store-turns.index'))->assertUnauthorized();
        $this->getJson(route('store-turns.show', rand()))->assertUnauthorized();
        $this->postJson(route('store-turns.store'))->assertUnauthorized();
        $this->putJson(route('store-turns.update', rand()))->assertUnauthorized();
        $this->deleteJson(route('store-turns.destroy', rand()))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function an_user_without_permission_cannot_access_to_store_turn_resources()
    {
        $this->signIn();
        
        $randomStoreTurnId = StoreTurn::all()->random()->id;

        $this->getJson(route('store-turns.index'))->assertForbidden();
        $this->getJson(route('store-turns.show', $randomStoreTurnId))->assertForbidden();
        $this->postJson(route('store-turns.store'))->assertForbidden();
        $this->putJson(route('store-turns.update', $randomStoreTurnId))->assertForbidden();
        $this->deleteJson(route('store-turns.destroy', $randomStoreTurnId))->assertForbidden();
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_all_store_turns()
    {
        $this->signInWithPermissionsTo(['store-turns.index']);

        $response = $this->getJson(route('store-turns.index'))
        ->assertOk();
        
        foreach (StoreTurn::limit(10)->get() as $storeTurn) {
            $response->assertJsonFragment($storeTurn->toArray());
        }
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_a_store_turn()
    {
        $this->signInWithPermissionsTo(['store-turns.show']);

        $storeTurn = factory(StoreTurn::class)->create();

        $this->getJson(route('store-turns.show', $storeTurn->id))
        ->assertOk();
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_store_a_store_turn()
    {
        $this->signInWithPermissionsTo(['store-turns.store']);

        $attributes = factory(StoreTurn::class)->raw();

        $this->postJson(route('store-turns.store'), $attributes)
        ->assertCreated();
    }


    /**
     * @test
     */
    public function an_user_with_permission_can_update_a_store_turn()
    {
        $this->signInWithPermissionsTo(['store-turns.update']);

        $storeTurn = factory(StoreTurn::class)->create();

        $attributes = factory(StoreTurn::class)->raw();

        $this->putJson(route('store-turns.update', $storeTurn->id), array_merge($attributes, ['closed_petty_cash_amount' => 1525.15]))
        ->assertOk();
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_destroy_a_store_turn()
    {
        $this->signInWithPermissionsTo(['store-turns.destroy']);

        $storeTurn = factory(StoreTurn::class)->create();

        $this->deleteJson(route('store-turns.destroy', $storeTurn->id))
        ->assertOk();

        $this->assertDatabaseMissing('store_turns', $storeTurn->toArray());
    }

}
