<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Presentation\Presentation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PresentationControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'PresentationSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_presentation_resources()
  {
    $this->getJson(route('presentations.index'))->assertUnauthorized();
    $this->getJson(route('presentations.show', rand()))->assertUnauthorized();
    $this->postJson(route('presentations.store'))->assertUnauthorized();
    $this->putJson(route('presentations.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('presentations.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_presentation_resources()
  {
    $this->signIn();
    
    $randomPresentationId = Presentation::all()->random()->id;

    $this->getJson(route('presentations.index'))->assertForbidden();
    $this->getJson(route('presentations.show', $randomPresentationId))->assertForbidden();
    $this->postJson(route('presentations.store'))->assertForbidden();
    $this->putJson(route('presentations.update', $randomPresentationId))->assertForbidden();
    $this->deleteJson(route('presentations.destroy', $randomPresentationId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_presentations()
  {

    $role = $this->getRoleWithPermissionsTo(['presentations.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('presentations.index'))
      ->assertOk();
    
    foreach (Presentation::limit(10)->get() as $Presentation) {
      $response->assertJsonFragment($Presentation->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_presentation()
  {
    $role = $this->getRoleWithPermissionsTo(['presentations.show']);
    $user = $this->signInWithRole($role);

    $Presentation = factory(Presentation::class)->create();

    $this->getJson(route('presentations.show', $Presentation->id))
      ->assertOk()
      ->assertJson($Presentation->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_presentation()
  {
    $role = $this->getRoleWithPermissionsTo(['presentations.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(Presentation::class)->raw();

    $this->postJson(route('presentations.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('presentations', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_presentation()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['presentations.update']);
    $user = $this->signInWithRole($role);

    $Presentation = factory(Presentation::class)->create();

    $attributes = factory(Presentation::class)->raw();

    $this->putJson(route('presentations.update', $Presentation->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('presentations', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_presentation()
  {
    $role = $this->getRoleWithPermissionsTo(['presentations.destroy']);
    $user = $this->signInWithRole($role);

    $Presentation = factory(Presentation::class)->create();

    $this->deleteJson(route('presentations.destroy', $Presentation->id))
      ->assertOk();

    $this->assertDatabaseMissing('presentations', $Presentation->toArray());
  }

}