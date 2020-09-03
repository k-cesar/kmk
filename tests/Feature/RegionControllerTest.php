<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Region\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegionControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RegionSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_region_resources()
  {
    $this->getJson(route('regions.index'))->assertUnauthorized();
    $this->getJson(route('regions.show', rand()))->assertUnauthorized();
    $this->postJson(route('regions.store'))->assertUnauthorized();
    $this->putJson(route('regions.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('regions.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_region_resources()
  {
    $this->signIn();
    
    $randomRegionId = Region::all()->random()->id;

    $this->getJson(route('regions.index'))->assertForbidden();
    $this->getJson(route('regions.show', $randomRegionId))->assertForbidden();
    $this->postJson(route('regions.store'))->assertForbidden();
    $this->putJson(route('regions.update', $randomRegionId))->assertForbidden();
    $this->deleteJson(route('regions.destroy', $randomRegionId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_regions()
  {
    $this->signInWithPermissionsTo(['regions.index']);

    $response = $this->getJson(route('regions.index'))
      ->assertOk();
    
    foreach (Region::limit(10)->get() as $region) {
      $response->assertJsonFragment($region->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_region()
  {
    $this->signInWithPermissionsTo(['regions.show']);

    $region = factory(Region::class)->create();

    $this->getJson(route('regions.show', $region->id))
      ->assertOk()
      ->assertJson($region->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_region()
  {
    $this->signInWithPermissionsTo(['regions.store']);

    $attributes = factory(Region::class)->raw();

    $this->postJson(route('regions.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('regions', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_region()
  {
    $this->signInWithPermissionsTo(['regions.update']);

    $region = factory(Region::class)->create();

    $attributes = factory(Region::class)->raw();

    $this->putJson(route('regions.update', $region->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('regions', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_region()
  {
    $this->signInWithPermissionsTo(['regions.destroy']);

    $region = factory(Region::class)->create();

    $this->deleteJson(route('regions.destroy', $region->id))
      ->assertOk();

    $this->assertDatabaseMissing('regions', $region->toArray());
  }
}