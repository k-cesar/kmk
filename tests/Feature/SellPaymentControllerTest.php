<?php

namespace Tests\Feature;

use App\Http\Modules\PaymentMethod\PaymentMethod;
use Tests\ApiTestCase;
use App\Http\Modules\Sell\Sell;
use App\Http\Modules\SellPayment\SellPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SellPaymentControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'PaymentMethodSeeder', 'SellPaymentSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_sell_payment_resources()
  {
    $this->getJson(route('sell-payments.index'))->assertUnauthorized();
    $this->putJson(route('sell-payments.update', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_sell_payment_resources()
  {
    $this->signIn();
    
    $randomSellPaymentId = SellPayment::all()->random()->id;

    $this->getJson(route('sell-payments.index'))->assertForbidden();
    $this->putJson(route('sell-payments.update', $randomSellPaymentId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_sell_payments()
  {
    $user = $this->signInWithPermissionsTo(['sell-payments.index']);

    $sell = Sell::whereHas('sellPayment')->first();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $sell->store->company_id]);
      } else {
        $user->stores()->sync($sell->store_id);
      }
    }

    $response = $this->getJson(route('sell-payments.index', ['store_id' => $sell->store_id]))
      ->assertOk();
    
    foreach (SellPayment::where('sell_id', $sell->id)->limit(10)->get() as $sellPayment) {
      $response->assertJsonFragment($sellPayment->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_sell_payment()
  {
    $user = $this->signInWithPermissionsTo(['sell-payments.update']);

    $sell = Sell::query()
      ->where('status', Sell::OPTION_STATUS_PENDING)
      ->whereHas('sellPayment')
      ->whereHas('storeTurn', function ($query) {
        return $query->where('is_open', true);
      })
      ->first();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $sell->store->company_id]);
      } else {
        $user->stores()->sync($sell->store_id);
      }
    }

    $paymentMethod = PaymentMethod::where('name', '!=', PaymentMethod::OPTION_PAYMENT_CREDIT)->first();

    $attributes = [
      'store_id'          => $sell->store_id,
      'store_turn_id'     => $sell->storeTurn->id,
      'payment_method_id' => $paymentMethod->id,
      'description'       => $paymentMethod->name,
    ];

    $this->putJson(route('sell-payments.update', $sell->sellPayment->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('sells', [
      'id'          => $sell->id,
      'description' => $paymentMethod->name,
    ]);

    $this->assertDatabaseHas('sell_payments', [
      'sell_id'           => $sell->id,
      'payment_method_id' => $paymentMethod->id,
    ]);
  }

}