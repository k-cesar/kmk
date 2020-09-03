<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Modules\SocioeconomicLevel\SocioeconomicLevel;

class SocioeconomicLevelControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'SocioeconomicLevelSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_socioeconomic_level_resources()
  {
    $this->getJson(route('socioeconomic-levels.index'))->assertUnauthorized();
    $this->getJson(route('socioeconomic-levels.show', rand()))->assertUnauthorized();
    $this->postJson(route('socioeconomic-levels.store'))->assertUnauthorized();
    $this->putJson(route('socioeconomic-levels.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('socioeconomic-levels.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_socioeconomic_level_resources()
  {
    $this->signIn();
    
    $randomSocioeconomicLevelId = SocioeconomicLevel::all()->random()->id;

    $this->getJson(route('socioeconomic-levels.index'))->assertForbidden();
    $this->getJson(route('socioeconomic-levels.show', $randomSocioeconomicLevelId))->assertForbidden();
    $this->postJson(route('socioeconomic-levels.store'))->assertForbidden();
    $this->putJson(route('socioeconomic-levels.update', $randomSocioeconomicLevelId))->assertForbidden();
    $this->deleteJson(route('socioeconomic-levels.destroy', $randomSocioeconomicLevelId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_socioeconomic_levels()
  {
    $this->signInWithPermissionsTo(['socioeconomic-levels.index']);

    $response = $this->getJson(route('socioeconomic-levels.index'))
      ->assertOk();
    
    foreach (SocioeconomicLevel::limit(10)->get() as $socioeconomicLevel) {
      $response->assertJsonFragment($socioeconomicLevel->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_socioeconomic_level()
  {
    $this->signInWithPermissionsTo(['socioeconomic-levels.show']);

    $socioeconomicLevel = factory(SocioeconomicLevel::class)->create();

    $this->getJson(route('socioeconomic-levels.show', $socioeconomicLevel->id))
      ->assertOk()
      ->assertJson($socioeconomicLevel->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_socioeconomic_level()
  {
    $this->signInWithPermissionsTo(['socioeconomic-levels.store']);

    $attributes = factory(SocioeconomicLevel::class)->raw();

    $this->postJson(route('socioeconomic-levels.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('socioeconomic_levels', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_socioeconomic_level()
  {
    $this->signInWithPermissionsTo(['socioeconomic-levels.update']);

    $socioeconomicLevel = factory(SocioeconomicLevel::class)->create();

    $attributes = factory(SocioeconomicLevel::class)->raw();

    $this->putJson(route('socioeconomic-levels.update', $socioeconomicLevel->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('socioeconomic_levels', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_socioeconomic_level()
  {
    $this->signInWithPermissionsTo(['socioeconomic-levels.destroy']);

    $socioeconomicLevel = factory(SocioeconomicLevel::class)->create();

    $this->deleteJson(route('socioeconomic-levels.destroy', $socioeconomicLevel->id))
      ->assertOk();

    $this->assertDatabaseMissing('socioeconomic_levels', $socioeconomicLevel->toArray());
  }

}