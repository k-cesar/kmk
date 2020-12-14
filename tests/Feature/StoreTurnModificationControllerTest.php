<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\StoreTurn\StoreTurn;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Http\Modules\StoreTurnModification\StoreTurnModification;

class StoreTurnModificationControllerTest extends ApiTestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(['PermissionSeeder', 'StoreTurnModificationSeeder']);
    }

    /**
     * @test
     */
    public function a_guest_cannot_access_to_store_turn_modification_resources()
    {
        $this->postJson(route('store-turn-modifications.store'))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function an_user_without_permission_cannot_access_to_store_turn_modification_resources()
    {
        $this->signIn();

        $this->postJson(route('store-turn-modifications.store'))->assertForbidden();
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_store_a_store_turn_modification()
    {
        $user = $this->signInWithPermissionsTo(['store-turn-modifications.store']);

        $storeTurn = factory(StoreTurn::class)->create(['is_open' => true]);
        $storeTurn->turn->update(['store_id' => $storeTurn->store_id]);

        if ($user->role->level > 1) {
            if ($user->role->level == 2) {
                $user->update(['company_id' => $storeTurn->store->company_id]);
            } else {
                $user->stores()->sync($storeTurn->store_id);
            }
        }

        $attributes = factory(StoreTurnModification::class)->raw([
            'store_id'          => $storeTurn->store_id,
            'store_turn_id'     => $storeTurn->id,
            'modification_type' => StoreTurnModification::OPTION_MODIFICATION_TYPE_CASH_PURCHASE,
        ]);

        $this->postJson(route('store-turn-modifications.store'), $attributes)
            ->assertCreated();

        $this->assertDatabaseHas('store_turn_modifications', $attributes);
    }

    
}
