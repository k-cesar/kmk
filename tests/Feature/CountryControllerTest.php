<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Country\Country;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CountryControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'CountrySeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_country_resources()
  {
    $this->getJson(route('countries.index'))->assertUnauthorized();
    $this->getJson(route('countries.show', rand()))->assertUnauthorized();
    $this->postJson(route('countries.store'))->assertUnauthorized();
    $this->putJson(route('countries.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('countries.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_country_resources()
  {
    $this->signIn();
    
    $randomCountryId = Country::all()->random()->id;

    $this->getJson(route('countries.index'))->assertForbidden();
    $this->getJson(route('countries.show', $randomCountryId))->assertForbidden();
    $this->postJson(route('countries.store'))->assertForbidden();
    $this->putJson(route('countries.update', $randomCountryId))->assertForbidden();
    $this->deleteJson(route('countries.destroy', $randomCountryId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_countries()
  {

    $role = $this->getRoleWithPermissionsTo(['countries.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('countries.index'))
      ->assertOk();
    
    foreach (Country::limit(10)->get() as $country) {
      $response->assertJsonFragment($country->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_country()
  {
    $role = $this->getRoleWithPermissionsTo(['countries.show']);
    $user = $this->signInWithRole($role);

    $country = factory(Country::class)->create();

    $this->getJson(route('countries.show', $country->id))
      ->assertOk()
      ->assertJson($country->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_country()
  {
    $role = $this->getRoleWithPermissionsTo(['countries.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(Country::class)->raw();

    $this->postJson(route('countries.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('countries', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_country()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['countries.update']);
    $user = $this->signInWithRole($role);

    $country = factory(Country::class)->create();

    $attributes = factory(Country::class)->raw();

    $this->putJson(route('countries.update', $country->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('countries', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_country()
  {
    $role = $this->getRoleWithPermissionsTo(['countries.destroy']);
    $user = $this->signInWithRole($role);

    $country = factory(Country::class)->create();

    $this->deleteJson(route('countries.destroy', $country->id))
      ->assertOk();

    $this->assertDatabaseMissing('countries', $country->toArray());
  }

}