<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Presentation\Presentation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PresentationTurnControllerTest extends ApiTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(['PermissionSeeder']);
    }

    /**
     * @test
     */
    public function a_guest_cannot_access_to_presentation_turn_resources()
    {
        $randomPresentationId = factory(Presentation::class)->create()->id;

        $this->getJson(route('presentations.turns.index', $randomPresentationId))->assertUnauthorized();
        $this->postJson(route('presentations.turns.store', $randomPresentationId))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function an_user_without_permission_cannot_access_to_presentation_turn_resources()
    {
        $this->signIn();

        $randomPresentationId = factory(Presentation::class)->create()->id;

        $this->getJson(route('presentations.turns.index', $randomPresentationId))->assertForbidden();
        $this->postJson(route('presentations.turns.store', $randomPresentationId))->assertForbidden();
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_all_presentation_turns()
    {
        $user = $this->signInWithPermissionsTo(['presentations.turns.index']);

        $user->role->update(['level' => rand(1, 2)]);

        $turn = factory(Turn::class)->create([
            'store_id' => factory(Store::class)->create(['company_id' => $user->company_id])
        ]);

        $presentation = factory(Presentation::class)->create(['company_id' => $user->company_id]);
        
        $presentation->turns()->attach($turn, ['price' => 123456789]);

        $response = $this->getJson(route('presentations.turns.index', $presentation->id))
            ->assertSuccessful();
        
        $response->assertSee($turn->name)
            ->assertSee($turn->name)
            ->assertSee($turn->start_time)
            ->assertSee($turn->end_time)
            ->assertSee(123456789);
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_store_a_presentation_turn()
    {
        $user = $this->signInWithPermissionsTo(['presentations.turns.store']);

        $user->role->update(['level' => 2]);

        $storeVisible    = factory(Store::class)->create(['company_id' => $user->company_id]);
        $storeNotVisible = factory(Store::class)->create(['company_id' => factory(Company::class)]);

        $turnToIgnore = factory(Turn::class)->create(['store_id' => $storeNotVisible->id]);
        $turnToDelete = factory(Turn::class)->create(['store_id' => $storeVisible->id]);
        $turnToUpdate = factory(Turn::class)->create(['store_id' => $storeVisible->id]);
        $turnToCreate = factory(Turn::class)->create(['store_id' => $storeVisible->id]);

        $presentation = factory(Presentation::class)->create(['company_id' => $user->company_id]);

        $presentation->turns()->attach($turnToIgnore, ['price' => 100]);
        $presentation->turns()->attach($turnToDelete, ['price' => 200]);
        $presentation->turns()->attach($turnToUpdate, ['price' => 300]);


        $attributes = [
            'apply_for_all' => 0,
            'global_price'  => 400,
            'prices'        => [
                [
                    'price'=> 500,
                    'turns'=> [['id' => $turnToUpdate->id]]
                ],
                [
                    'price'=> 600,
                    'turns'=> [['id' => $turnToCreate->id]]
                ],
            ]
        ];

        $this->postJson(route('presentations.turns.store', $presentation->id), $attributes)
            ->assertOk();

        $this->assertDatabaseHas('presentations_turns', [
            'presentation_id' => $presentation->id,
            'turn_id'         => $turnToIgnore->id,
            'price'           => 100,
        ]);

        $this->assertDatabaseMissing('presentations_turns', [
            'presentation_id' => $presentation->id,
            'turn_id'         => $turnToDelete->id,
            'price'           => 200,
        ]);

        $this->assertDatabaseHas('presentations_turns', [
            'presentation_id' => $presentation->id,
            'turn_id'         => $turnToUpdate->id,
            'price'           => 500,
        ]);

        $this->assertDatabaseHas('presentations_turns', [
            'presentation_id' => $presentation->id,
            'turn_id'         => $turnToCreate->id,
            'price'           => 600,
        ]);
        
    }
}