<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\StoreTurn\StoreTurn;
use App\Http\Modules\Presentation\Presentation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Modules\PresentationCombo\PresentationCombo;

class StoreTurnItemControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'TurnSeeder', 'PresentationSeeder', 'PresentationComboSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_store_turn_item_resources()
  {
    $turn = Turn::inRandomOrder()->first();

    $this->getJson(route('stores.turns.items.index', [$turn->store_id, $turn->id]))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_store_turn_item_resources()
  {
    $this->signIn();

    $turn = Turn::inRandomOrder()->first();
    
    $this->getJson(route('stores.turns.items.index', [$turn->store_id, $turn->id]))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_store_turn_items()
  {
    $user = $this->signInWithPermissionsTo(['stores.turns.items.index']);

    $storeTurn = factory(StoreTurn::class)->create(['is_open' => true]);

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $storeTurn->store->company_id]);
      } else {
        $user->stores()->sync($storeTurn->store_id);
      }
    }

    $response = $this->getJson(route('stores.turns.items.index', [$storeTurn->store_id, $storeTurn->turn_id]))
      ->assertOk();

    $presentation = Presentation::limit(5)->get();
    $combos = PresentationCombo::limit(5)->get(['id', 'description', 'suggested_price AS price']);

    $items = $presentation->merge($combos);

    $items->each(function ($item) use ($response) {
      $response->assertJsonFragment([
        'id'          => "$item->id",
        'description' => $item->description,
        'price'       => $item->price,
        'type'        => $item instanceof Presentation ? 'PRESENTATION' : 'COMBO',
      ]);
    });
  }
}