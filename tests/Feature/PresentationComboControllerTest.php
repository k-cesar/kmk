<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Presentation\Presentation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Modules\PresentationCombo\PresentationCombo;

class PresentationComboControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'PresentationComboSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_presentation_combo_resources()
  {
    $this->getJson(route('presentation-combos.index'))->assertUnauthorized();
    $this->getJson(route('presentation-combos.show', rand()))->assertUnauthorized();
    $this->postJson(route('presentation-combos.store'))->assertUnauthorized();
    $this->putJson(route('presentation-combos.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('presentation-combos.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_presentation_combo_resources()
  {
    $this->signIn();
    
    $randomPresentationComboId = PresentationCombo::all()->random()->id;

    $this->getJson(route('presentation-combos.index'))->assertForbidden();
    $this->getJson(route('presentation-combos.show', $randomPresentationComboId))->assertForbidden();
    $this->postJson(route('presentation-combos.store'))->assertForbidden();
    $this->putJson(route('presentation-combos.update', $randomPresentationComboId))->assertForbidden();
    $this->deleteJson(route('presentation-combos.destroy', $randomPresentationComboId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_presentation_combos()
  {
    $this->signInWithPermissionsTo(['presentation-combos.index']);

    $response = $this->getJson(route('presentation-combos.index'))
      ->assertOk();
    
    foreach (PresentationCombo::limit(10)->get() as $presentationCombo) {
      $response->assertJsonFragment($presentationCombo->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_presentation_combo()
  {
    $this->signInWithPermissionsTo(['presentation-combos.show']);

    $presentationCombo = factory(PresentationCombo::class)->create();

    $this->getJson(route('presentation-combos.show', $presentationCombo->id))
      ->assertOk()
      ->assertJson($presentationCombo->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_presentation_combo()
  {
    $user = $this->signInWithPermissionsTo(['presentation-combos.store']);

    $user->company->update(['allow_add_products' => true]);

    $stores = factory(Store::class, 2)->create();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $stores->each(function ($store) use ($user) {
          $store->update(['company_id' => $user->company_id]);
        });
      } else {
        $user->stores()->sync($stores->pluck('id'));
      }
    }

    $attributes = factory(PresentationCombo::class)->raw(['company_id' => $user->company_id]);
    $extraAttributes['presentations'] = factory(Presentation::class, 2)->create(['company_id' => $user->company_id])->pluck('id')->toArray();
    $extraAttributes['prices'] = [
      [
        'suggested_price' => 50,
        'store_id'        => $stores->first()->id,
        'turns'           => factory(Turn::class, 2)->create(['store_id' => $stores->first()->id])->pluck('id')->toArray(),
      ],
      [
        'suggested_price' => 23,
        'store_id'        => $stores->last()->id,
        'turns'           => factory(Turn::class, 2)->create(['store_id' => $stores->last()->id])->pluck('id')->toArray(),
      ],
    ];

    $this->postJson(route('presentation-combos.store'), array_merge($attributes, $extraAttributes))
      ->assertCreated();
    
    $this->assertDatabaseHas('presentation_combos', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_presentation_combo()
  {
    $user = $this->signInWithPermissionsTo(['presentation-combos.update']);

    $stores = factory(Store::class, 2)->create();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $stores->each(function ($store) use ($user) {
          $store->update(['company_id' => $user->company_id]);
        });
      } else {
        $user->stores()->sync($stores->pluck('id'));
      }
    }

    $presentationCombo = factory(PresentationCombo::class)->create(['company_id' => $user->company_id]);

    $attributes = factory(PresentationCombo::class)->raw(['company_id' => $presentationCombo->company_id]);
    $extraAttributes['presentations'] = factory(Presentation::class, 2)->create(['company_id' => $user->company_id])->pluck('id')->toArray();
    $extraAttributes['prices'] = [
      [
        'suggested_price' => 50,
        'store_id'        => $stores->first()->id,
        'turns'           => factory(Turn::class, 2)->create(['store_id' => $stores->first()->id])->pluck('id')->toArray(),
      ],
      [
        'suggested_price' => 23,
        'store_id'        => $stores->last()->id,
        'turns'           => factory(Turn::class, 2)->create(['store_id' => $stores->last()->id])->pluck('id')->toArray(),
      ],
    ];

    $this->putJson(route('presentation-combos.update', $presentationCombo->id), array_merge($attributes, $extraAttributes))
      ->assertOk();

    $this->assertDatabaseHas('presentation_combos', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_presentation_combo()
  {
    $user = $this->signInWithPermissionsTo(['presentation-combos.destroy']);

    $presentationCombo = factory(PresentationCombo::class)->create(['company_id' => $user->company_id]);

    $this->deleteJson(route('presentation-combos.destroy', $presentationCombo->id))
      ->assertOk();

    $this->assertDatabaseMissing('presentation_combos', $presentationCombo->toArray());
  }

}