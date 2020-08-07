<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Uom\Uom;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UomControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'UomSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_uom_resources()
  {
    $this->getJson(route('uoms.index'))->assertUnauthorized();
    $this->getJson(route('uoms.show', rand()))->assertUnauthorized();
    $this->postJson(route('uoms.store'))->assertUnauthorized();
    $this->putJson(route('uoms.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('uoms.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_uom_resources()
  {
    $this->signIn();
    
    $randomUomId = Uom::all()->random()->id;

    $this->getJson(route('uoms.index'))->assertForbidden();
    $this->getJson(route('uoms.show', $randomUomId))->assertForbidden();
    $this->postJson(route('uoms.store'))->assertForbidden();
    $this->putJson(route('uoms.update', $randomUomId))->assertForbidden();
    $this->deleteJson(route('uoms.destroy', $randomUomId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_uoms()
  {
    $this->signInWithPermissionsTo(['uoms.index']);

    $response = $this->getJson(route('uoms.index'))
      ->assertOk();
    
    foreach (Uom::limit(10)->get() as $uom) {
      $response->assertJsonFragment($uom->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_uom()
  {
    $this->signInWithPermissionsTo(['uoms.show']);

    $uom = factory(Uom::class)->create();

    $this->getJson(route('uoms.show', $uom->id))
      ->assertOk()
      ->assertJson($uom->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_uom()
  {
    $this->signInWithPermissionsTo(['uoms.store']);

    $attributes = factory(Uom::class)->raw();

    $this->postJson(route('uoms.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('uoms', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_uom()
  {
    $this->signInWithPermissionsTo(['uoms.update']);

    $uom = factory(Uom::class)->create();

    $attributes = factory(Uom::class)->raw();

    $this->putJson(route('uoms.update', $uom->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('uoms', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_uom()
  {
    $this->signInWithPermissionsTo(['uoms.destroy']);

    $uom = factory(Uom::class)->create();

    $this->deleteJson(route('uoms.destroy', $uom->id))
      ->assertOk();

    $this->assertDatabaseMissing('uoms', $uom->toArray());
  }
}