<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\PresentationSku\PresentationSku;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PresentationSkuControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'PresentationSkuSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_presentation_sku_resources()
  {
    $this->getJson(route('presentation-skus.index'))->assertUnauthorized();
    $this->getJson(route('presentation-skus.show', rand()))->assertUnauthorized();
    $this->postJson(route('presentation-skus.store'))->assertUnauthorized();
    $this->putJson(route('presentation-skus.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('presentation-skus.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_presentation_sku_resources()
  {
    $this->signIn();
    
    $randomPresentationSkuId = PresentationSku::all()->random()->id;

    $this->getJson(route('presentation-skus.index'))->assertForbidden();
    $this->getJson(route('presentation-skus.show', $randomPresentationSkuId))->assertForbidden();
    $this->postJson(route('presentation-skus.store'))->assertForbidden();
    $this->putJson(route('presentation-skus.update', $randomPresentationSkuId))->assertForbidden();
    $this->deleteJson(route('presentation-skus.destroy', $randomPresentationSkuId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_presentation_skus()
  {
    $this->signInWithPermissionsTo(['presentation-skus.index']);

    $response = $this->getJson(route('presentation-skus.index'))
      ->assertOk();
    
    foreach (PresentationSku::limit(10)->get() as $presentationSku) {
      $response->assertJsonFragment($presentationSku->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_presentation_sku()
  {
    $this->signInWithPermissionsTo(['presentation-skus.show']);

    $presentationSku = factory(PresentationSku::class)->create();

    $this->getJson(route('presentation-skus.show', $presentationSku->id))
      ->assertOk()
      ->assertJson($presentationSku->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_presentation_sku()
  {
    $this->signInWithPermissionsTo(['presentation-skus.store']);

    $attributes = factory(PresentationSku::class)->raw();

    $this->postJson(route('presentation-skus.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('presentation_skus', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_presentation_sku()
  {
    $this->signInWithPermissionsTo(['presentation-skus.update']);

    $presentationSku = factory(PresentationSku::class)->create();

    $attributes = factory(PresentationSku::class)->raw();

    $this->putJson(route('presentation-skus.update', $presentationSku->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('presentation_skus', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_presentation_sku()
  {
    $this->signInWithPermissionsTo(['presentation-skus.destroy']);

    $presentationSku = factory(PresentationSku::class)->create();

    $this->deleteJson(route('presentation-skus.destroy', $presentationSku->id))
      ->assertOk();

    $this->assertDatabaseMissing('presentation_skus', $presentationSku->toArray());
  }

}