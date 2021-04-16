<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Presentation\Presentation;
use App\Http\Modules\PresentationSku\PresentationSku;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PresentationControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'PresentationSeeder']);
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
  public function an_user_with_permission_can_see_all_presentations()
  {
    $this->signInWithPermissionsTo(['presentations.index']);

    $response = $this->getJson(route('presentations.index'))
      ->assertOk();
    
    foreach (Presentation::limit(10)->get() as $presentation) {
      $response->assertJsonFragment($presentation->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_presentation()
  {
    $this->signInWithPermissionsTo(['presentations.show']);

    $presentation = factory(Presentation::class)->create();

    $this->getJson(route('presentations.show', $presentation->id))
      ->assertOk()
      ->assertJson($presentation->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_presentation()
  {
    $user = $this->signInWithPermissionsTo(['presentations.store']);

    $user->company->update(['allow_add_products' => true]);

    $attributes = factory(Presentation::class)->raw(['company_id' => $user->company_id]);

    $this->postJson(route('presentations.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('presentations', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_presentation()
  {
    $user = $this->signInWithPermissionsTo(['presentations.update']);

    $presentation = factory(Presentation::class)->create();

    $attributes = factory(Presentation::class)->raw(['company_id' => $presentation->company_id]);

    $this->putJson(route('presentations.update', $presentation->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('presentations', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_presentation()
  {
    $this->signInWithPermissionsTo(['presentations.destroy']);

    $presentation = factory(Presentation::class)->create();

    $this->deleteJson(route('presentations.destroy', $presentation->id))
      ->assertOk();

    $this->assertDatabaseMissing('presentations', $presentation->toArray());

    $presentacionSku = factory(PresentationSku::class)->create();
    $presentationWithSKU = $presentacionSku->presentation;

    $this->deleteJson(route('presentations.destroy', $presentationWithSKU->id))
      ->assertStatus(409);
  }

  /**
   * @test
   */
  public function an_user_can_see_all_presentations_options()
  {
    $user = $this->signIn();

    $response = $this->getJson(route('presentations.options'))
      ->assertOk();

    $presentations = presentation::select('id', 'description')
      ->withOut('product')
      ->visibleThroughCompany($user)
      ->filterByDescriptionOrSkuCode(request('presentation_description'), request('sku_code'))
      ->limit(10)
      ->get();

    foreach ($presentations as $presentation) {
      $response->assertJsonFragment($presentation->toArray());
    }
  }

}