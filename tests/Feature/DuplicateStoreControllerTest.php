<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Presentation\Presentation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Modules\PresentationCombo\PresentationCombo;
use App\Http\Modules\PresentationCombo\PresentationComboStoreTurn;

class DuplicateStoreControllerTest extends ApiTestCase
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
  public function a_guest_cannot_access_to_stores_duplicate_resources()
  {
    $this->postJson(route('stores-duplicate.store', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_stores_duplicate_resources()
  {
    $this->signIn();
    
    $this->postJson(route('stores-duplicate.store', rand()))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_duplate_a_store()
  {
    $user = $this->signInWithPermissionsTo(['stores-duplicate.store']);

    $user->company->update(['allow_add_stores' => true]);
    $store = factory(Store::class)->create();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $store->update(['company_id' => $user->company_id]);
      } else {
        $user->stores()->sync($store->id);
      }
    }

    $turns = factory(Turn::class, 2)->create(['store_id' => $store->id]);
    $presentations = factory(Presentation::class, 2)->create();
    $presentationCombo = factory(PresentationCombo::class)->create();

    $presentations->first()->turns()->sync([$turns->first()->id => ['price'  => 100]]);
    $presentations->last()->turns()->sync( [$turns->last()->id  => ['price'  => 400]]);

    PresentationComboStoreTurn::create([
      'presentation_combo_id' => $presentationCombo->id,
      'store_id'              => $store->id,
      'turn_id'               => $turns->first()->id,
      'suggested_price'       => 500,
    ]);
    
    $attributes = factory(Store::class)->raw();

    $this->postJson(route('stores-duplicate.store'), array_merge($attributes, ['store_id' => $store->id]))
      ->assertCreated();
    
    $this->assertDatabaseHas('stores', $attributes);
    
    foreach ($turns as $turn) {
      $this->assertDatabaseHas('turns', [
        'store_id'   => 2,
        'name'       => $turn->name,
        'start_time' => $turn->start_time,
        'end_time'   => $turn->end_time,
        'is_active'  => $turn->is_active,
        'is_default' => $turn->is_default,
        ]);
      }
      
      $this->assertDatabaseHas('presentations_turns', [
        'presentation_id' => $presentations->first()->id,
        'turn_id'         => $presentations->first()->turns[1]->id,
        'price'           => 100,
      ]);

      $this->assertDatabaseHas('presentations_turns', [
        'presentation_id' => $presentations->last()->id,
        'turn_id'         => $presentations->last()->turns[1]->id,
        'price'           => 400,
      ]);

      $this->assertDatabaseHas('presentation_combos_stores_turns', [
        'presentation_combo_id' => $presentationCombo->id,
        'store_id'              => 2,
        'turn_id'               => $presentationCombo->presentationCombosStoresTurns->last()->turn_id,
        'suggested_price'       => 500,
      ]);
  }

}