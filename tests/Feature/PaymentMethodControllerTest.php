<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\PaymentMethod\PaymentMethod;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PaymentMethodControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'CompanySeeder', 'PaymentMethodSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_payment_method_resources()
  {
    $this->getJson(route('payment-methods.index'))->assertUnauthorized();
    $this->getJson(route('payment-methods.show', rand()))->assertUnauthorized();
    $this->postJson(route('payment-methods.store'))->assertUnauthorized();
    $this->putJson(route('payment-methods.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('payment-methods.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_payment_method_resources()
  {
    $this->signIn();
    
    $randomPaymentMethodId = PaymentMethod::all()->random()->id;

    $this->getJson(route('payment-methods.index'))->assertForbidden();
    $this->getJson(route('payment-methods.show', $randomPaymentMethodId))->assertForbidden();
    $this->postJson(route('payment-methods.store'))->assertForbidden();
    $this->putJson(route('payment-methods.update', $randomPaymentMethodId))->assertForbidden();
    $this->deleteJson(route('payment-methods.destroy', $randomPaymentMethodId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_payment_methods()
  {

    $role = $this->getRoleWithPermissionsTo(['payment-methods.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('payment-methods.index'))
      ->assertOk();
    
    foreach (PaymentMethod::limit(10)->get() as $paymentMethod) {
      $response->assertJsonFragment($paymentMethod->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_payment_method()
  {
    $role = $this->getRoleWithPermissionsTo(['payment-methods.show']);
    $user = $this->signInWithRole($role);

    $paymentMethod = factory(PaymentMethod::class)->create();

    $this->getJson(route('payment-methods.show', $paymentMethod->id))
      ->assertOk()
      ->assertJson($paymentMethod->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_payment_method()
  {
    $role = $this->getRoleWithPermissionsTo(['payment-methods.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(PaymentMethod::class)->raw();

    $this->postJson(route('payment-methods.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('payment_methods', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_payment_method()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['payment-methods.update']);
    $user = $this->signInWithRole($role);

    $paymentMethod = factory(PaymentMethod::class)->create();

    $attributes = factory(PaymentMethod::class)->raw();

    $this->putJson(route('payment-methods.update', $paymentMethod->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('payment_methods', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_payment_method()
  {
    $role = $this->getRoleWithPermissionsTo(['payment-methods.destroy']);
    $user = $this->signInWithRole($role);

    $paymentMethod = factory(PaymentMethod::class)->create();

    $this->deleteJson(route('payment-methods.destroy', $paymentMethod->id))
      ->assertOk();

    $this->assertDatabaseMissing('payment_methods', $paymentMethod->toArray());
  }

}