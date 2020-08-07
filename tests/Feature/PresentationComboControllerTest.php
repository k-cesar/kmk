<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\PresentationCombo\PresentationCombo;
use App\Http\Modules\Presentation\Presentation;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Turn\Turn;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PresentationComboControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

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
    $this->signInWithPermissionsTo(['presentation-combos.store']);

    $attributes = factory(PresentationCombo::class)->raw();
    $extraAttributes['presentations'] = factory(Presentation::class, 2)->create()->pluck('id')->toArray();
    $extraAttributes['prices'] = [
      [
        'suggested_price' => 50,
        'store_id' => factory(Store::class)->create()->id,
        'turns' => factory(Turn::class, 2)->create()->pluck('id')->toArray(),
      ],
      [
        'suggested_price' => 23,
        'store_id' => factory(Store::class)->create()->id,
        'turns' => factory(Turn::class, 2)->create()->pluck('id')->toArray(),
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
    $this->signInWithPermissionsTo(['presentation-combos.update']);

    $presentationCombo = factory(PresentationCombo::class)->create();

    $attributes = factory(PresentationCombo::class)->raw();
    $extraAttributes['presentations'] = factory(Presentation::class, 2)->create()->pluck('id')->toArray();
    $extraAttributes['prices'] = [
      [
        'suggested_price' => 50,
        'store_id' => factory(Store::class)->create()->id,
        'turns' => factory(Turn::class, 2)->create()->pluck('id')->toArray(),
      ],
      [
        'suggested_price' => 23,
        'store_id' => factory(Store::class)->create()->id,
        'turns' => factory(Turn::class, 2)->create()->pluck('id')->toArray(),
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
    $this->signInWithPermissionsTo(['presentation-combos.destroy']);

    $presentationCombo = factory(PresentationCombo::class)->create();

    $this->deleteJson(route('presentation-combos.destroy', $presentationCombo->id))
      ->assertOk();

    $this->assertDatabaseMissing('presentation_combos', $presentationCombo->toArray());
  }

}