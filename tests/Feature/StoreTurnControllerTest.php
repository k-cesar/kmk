<?php

namespace Tests\Feature;

use App\Http\Modules\PaymentMethod\PaymentMethod;
use Tests\ApiTestCase;
use App\Http\Modules\Turn\Turn;
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

        $this->seed(['PermissionSeeder','StoreTurnSeeder']);
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
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_all_store_turns()
    {
        $user = $this->signInWithPermissionsTo(['store-turns.index']);

        $response = $this->getJson(route('store-turns.index'))
            ->assertOk();
        
        foreach (StoreTurn::visibleThroughStore($user)->limit(10)->get() as $storeTurn) {
            $response->assertJsonFragment($storeTurn->toArray());
        }
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_a_store_turn()
    {
        factory(PaymentMethod::class)->create(['name' => PaymentMethod::OPTION_PAYMENT_CARD,]);
        factory(PaymentMethod::class)->create(['name' => PaymentMethod::OPTION_PAYMENT_CASH,]);

        $user = $this->signInWithPermissionsTo(['store-turns.show']);

        $storeTurn = factory(StoreTurn::class)->create();

        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                $user->update(['company_id' => $storeTurn->store()->first()->company_id]);
            } else {
                $user->stores()->sync($storeTurn->store_id);
            }
        }

        $this->getJson(route('store-turns.show', $storeTurn->id))
            ->assertOk();
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_store_a_store_turn()
    {
        $user = $this->signInWithPermissionsTo(['store-turns.store']);

        $turn = factory(Turn::class)->create();

        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                $user->update(['company_id' => $turn->store->company_id]);
            } else {
                $user->stores()->sync($turn->store_id);
            }
        }

        foreach (StoreTurn::where('store_id', $turn->store_id)->get() as $storeTurn) {
            $storeTurn->update(['is_open' => false]);
        }

        $attributes = factory(StoreTurn::class)->raw([
            'store_id' => $turn->store_id,
            'turn_id'  => $turn->id,
        ]);

        $this->postJson(route('store-turns.store'), $attributes)
            ->assertCreated();
    }


    /**
     * @test
     */
    public function an_user_with_permission_can_update_a_store_turn()
    {
        $user = $this->signInWithPermissionsTo(['store-turns.update']);

        factory(PaymentMethod::class)->create(['name' => PaymentMethod::OPTION_PAYMENT_CARD,]);
        factory(PaymentMethod::class)->create(['name' => PaymentMethod::OPTION_PAYMENT_CASH,]);

        $turn = factory(Turn::class)->create();

        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                $user->update(['company_id' => $turn->store->company_id]);
            } else {
                $user->stores()->sync($turn->store_id);
            }
        }

        $storeTurn = factory(StoreTurn::class)->create([
            'store_id' => $turn->store_id,
            'is_open'  => true,
        ]);

        $attributes = factory(StoreTurn::class)->raw([
            'store_id' => $turn->store_id,
            'turn_id'  => $turn->id,
        ]);

        $this->putJson(route('store-turns.update', $storeTurn->id), array_merge($attributes, ['closed_petty_cash_amount' => 1525.15]))
            ->assertOk();
    }

}
