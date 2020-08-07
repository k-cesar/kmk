<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Company\Company;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CompanyControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'CompanySeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_company_resources()
  {
    $this->getJson(route('companies.index'))->assertUnauthorized();
    $this->getJson(route('companies.show', rand()))->assertUnauthorized();
    $this->postJson(route('companies.store'))->assertUnauthorized();
    $this->putJson(route('companies.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('companies.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_company_resources()
  {
    $this->signIn();

    $randomCompanyId = Company::all()->random()->id;

    $this->getJson(route('companies.index'))->assertForbidden();
    $this->getJson(route('companies.show', $randomCompanyId))->assertForbidden();
    $this->postJson(route('companies.store'))->assertForbidden();
    $this->putJson(route('companies.update', $randomCompanyId))->assertForbidden();
    $this->deleteJson(route('companies.destroy', $randomCompanyId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_companies()
  {
    $this->signInWithPermissionsTo(['companies.index']);

    $response = $this->getJson(route('companies.index'))
      ->assertOk();
    
    foreach (Company::limit(10)->get() as $company) {
      $response->assertJsonFragment($company->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_company()
  {
    $this->signInWithPermissionsTo(['companies.show']);

    $company = factory(Company::class)->create();

    $this->getJson(route('companies.show', $company->id))
      ->assertOk()
      ->assertJson($company->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_company()
  {
    $this->signInWithPermissionsTo(['companies.store']);

    $attributes = factory(Company::class)->raw();

    $this->postJson(route('companies.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('companies', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_company()
  {
    $this->signInWithPermissionsTo(['companies.update']);

    $company = factory(Company::class)->create();

    $attributes = factory(Company::class)->raw();

    $this->putJson(route('companies.update', $company->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('companies', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_company()
  {
    $this->signInWithPermissionsTo(['companies.destroy']);

    $company = factory(Company::class)->create();

    $this->deleteJson(route('companies.destroy', $company->id))
      ->assertOk();

    $this->assertDatabaseMissing('companies', $company->toArray());
  }

  /**
   * @test
   */
  public function an_user_can_see_all_companies_options()
  {
    $user = $this->signIn();

    $response = $this->getJson(route('companies.options'))
      ->assertOk();
    
    $companies = Company::select(['id', 'name'])
      ->withOut('country', 'currency')
      ->limit(10)
      ->get();

    foreach ($companies as $company) {
      $response->assertJsonFragment($company->toArray());
    }
  }

}