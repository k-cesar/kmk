<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Maker\Maker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MakerControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'MakerSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_maker_resources()
  {
    $this->getJson(route('makers.index'))->assertUnauthorized();
    $this->getJson(route('makers.show', rand()))->assertUnauthorized();
    $this->postJson(route('makers.store'))->assertUnauthorized();
    $this->putJson(route('makers.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('makers.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_maker_resources()
  {
    $this->signIn();
    
    $randomMakerId = Maker::all()->random()->id;

    $this->getJson(route('makers.index'))->assertForbidden();
    $this->getJson(route('makers.show', $randomMakerId))->assertForbidden();
    $this->postJson(route('makers.store'))->assertForbidden();
    $this->putJson(route('makers.update', $randomMakerId))->assertForbidden();
    $this->deleteJson(route('makers.destroy', $randomMakerId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_makers()
  {
    $user = $this->signInWithPermissionsTo(['makers.index']);

    $response = $this->getJson(route('makers.index'))
      ->assertOk();
    
    foreach (Maker::whereHasCompanyVisible($user)->limit(10)->get() as $maker) {
      $response->assertJsonFragment($maker->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_maker()
  {
    $user = $this->signInWithPermissionsTo(['makers.show']);

    $maker = factory(Maker::class)->create(['company_id' => $user->company_id]);

    $this->getJson(route('makers.show', $maker->id))
      ->assertOk()
      ->assertJson($maker->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_maker()
  {
    $user = $this->signInWithPermissionsTo(['makers.store']);

    $attributes = factory(Maker::class)->raw(['company_id' => $user->company_id]);

    $this->postJson(route('makers.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('makers', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_maker()
  {
    $user = $this->signInWithPermissionsTo(['makers.update']);

    $maker = factory(Maker::class)->create(['company_id' => $user->company_id]);

    $attributes = factory(Maker::class)->raw(['company_id' => $user->company_id]);

    $this->putJson(route('makers.update', $maker->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('makers', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_maker()
  {
    $user = $this->signInWithPermissionsTo(['makers.destroy']);

    $maker = factory(Maker::class)->create(['company_id' => $user->company_id]);

    $this->deleteJson(route('makers.destroy', $maker->id))
      ->assertOk();

    $this->assertDatabaseMissing('makers', $maker->toArray());
  }

  /**
   * @test
   */
  public function an_user_can_see_all_makers_options()
  {
    $user = $this->signIn();

    $response = $this->getJson(route('makers.options'))
      ->assertOk();

    $makers = Maker::select(['id', 'name'])
      ->visibleThroughCompany($user)
      ->limit(10)
      ->get();

    foreach ($makers as $maker) {
      $response->assertJsonFragment($maker->toArray());
    }
  }

}