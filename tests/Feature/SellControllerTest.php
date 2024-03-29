<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Sell\Sell;
use Illuminate\Support\Facades\DB;
use App\Http\Modules\Sell\SellInvoice;
use App\Http\Modules\StoreTurn\StoreTurn;
use App\Http\Modules\SellPayment\SellPayment;
use App\Http\Modules\Presentation\Presentation;
use App\Http\Modules\PaymentMethod\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Modules\PresentationCombo\PresentationCombo;

class SellControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'SellSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_sell_resources()
  {
    $this->getJson(route('sells.index'))->assertUnauthorized();
    $this->getJson(route('sells.show', rand()))->assertUnauthorized();
    $this->postJson(route('sells.store'))->assertUnauthorized();
    $this->deleteJson(route('sells.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_sell_resources()
  {
    $this->signIn();
    
    $randomSellId = Sell::all()->random()->id;

    $this->getJson(route('sells.index'))->assertForbidden();
    $this->getJson(route('sells.show', $randomSellId))->assertForbidden();
    $this->postJson(route('sells.store'))->assertForbidden();
    $this->deleteJson(route('sells.destroy', $randomSellId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_sells()
  {
    $user = $this->signInWithPermissionsTo(['sells.index']);

    $store = Sell::all()->first()->store;

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $store->company_id]);
      } else {
        $user->stores()->sync($store->id);
      }
    }

    $response = $this->getJson(route('sells.index', ['store_id' => $store->id, 'strict_search' => true]))
      ->assertOk();
    
    foreach (Sell::limit(10)->get() as $sell) {
      $response->assertJsonFragment($sell->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_sell()
  {
    $user = $this->signInWithPermissionsTo(['sells.show']);

    $sell = factory(Sell::class)->create();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $sell->store()->first()->company_id]);
      } else {
        $user->stores()->sync($sell->store_id);
      }
    }

    $this->getJson(route('sells.show', $sell->id))
      ->assertOk()
      ->assertJson($sell->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_sell()
  {
    $user = $this->signInWithPermissionsTo(['sells.store']);

    $storeTurn = factory(StoreTurn::class)->create(['is_open' => true]);

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $storeTurn->store->company_id]);
      } else {
        $user->stores()->sync($storeTurn->store_id);
      }
    }

    $presentationA = factory(Presentation::class)->create(['price' => 5]);

    $presentationB = factory(Presentation::class)->create(['price' => 1]);

    $combo = factory(PresentationCombo::class)->create(['suggested_price' => 5.25]);

    $combo->presentations()->sync([$presentationA->id, $presentationB->id]);

    DB::table('presentations_turns')
      ->insert([
        'presentation_id' => $presentationA->id,
        'turn_id'         => $storeTurn->turn_id,
        'price'           => 2.5,
      ]);

    $attributes = [
      'store_id'          => $storeTurn->store_id,
      'payment_method_id' => factory(PaymentMethod::class)->create()->id,
      'name'              => 'test',
      'nit'               => 'abc123abc',
      'address'           => 'Ciudad Test',
      'phone'             => '88888888',
      'email'             => 'test@test.com',
      'store_turn_id'     => $storeTurn->id,
      'items'             => [
        [
          'id'         => $presentationA->id,
          'quantity'   => 2,
          'type'       => 'PRESENTATION',
          'unit_price' => $presentationA->price,
        ],
        [
          'id'         => $presentationB->id,
          'quantity'   => 3,
          'type'       => 'PRESENTATION',
          'unit_price' => $presentationB->price,
        ],
        [
          'id'         => $combo->id,
          'quantity'   => 1,
          'type'       => 'COMBO',
          'unit_price' => $combo->suggested_price,
        ],
      ]
    ];

    $this->postJson(route('sells.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('sells', [
      'store_id'      => $storeTurn->store_id,
      'total'         => 13.25,
      'seller_id'     => $user->id,
      'store_turn_id' => $storeTurn->id,
    ]);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_offline_sell()
  {
    $seller = $this->signInWithPermissionsTo(['sells-offline.store']);

    $storeTurn = factory(StoreTurn::class)->create(['is_open' => false]);

    if ($seller->role->level > 1) {
      if ($seller->role->level == 2) {
        $seller->update(['company_id' => $storeTurn->store->company_id]);
      } else {
        $seller->stores()->sync($storeTurn->store_id);
      }
    }

    $presentationA = factory(Presentation::class)->create(['price' => 5]);

    $presentationB = factory(Presentation::class)->create(['price' => 1]);

    $combo = factory(PresentationCombo::class)->create(['suggested_price' => 5.25]);

    $combo->presentations()->sync([$presentationA->id, $presentationB->id]);

    DB::table('presentations_turns')
      ->insert([
        'turn_id'         => $storeTurn->turn_id,
        'presentation_id' => $presentationA->id,
        'price'           => 2.5,
      ]);

    $sellA = [
      'seller_id'         => $seller->id,
      'payment_method_id' => factory(PaymentMethod::class)->create()->id,
      'name'              => 'test',
      'nit'               => 'abc123abc',
      'address'           => 'Ciudad Test',
      'phone'             => '88888888',
      'email'             => 'test@test.com',
      'store_turn_id'     => $storeTurn->id,
      'items'             => [
        [
          'id'         => $presentationA->id,
          'quantity'   => 2,
          'type'       => 'PRESENTATION',
          'unit_price' => $presentationA->price,
        ],
        [
          'id'         => $presentationB->id,
          'quantity'   => 3,
          'type'       => 'PRESENTATION',
          'unit_price' => $presentationB->price,
        ],
        [
          'id'         => $combo->id,
          'quantity'   => 1,
          'type'       => 'COMBO',
          'unit_price' => $combo->suggested_price,
        ],
      ]
    ];

    $presentationC = factory(Presentation::class)->create(['price' => 20]);

    $sellB = [
      'seller_id'         => $seller->id,
      'payment_method_id' => factory(PaymentMethod::class)->create()->id,
      'name'              => 'test2',
      'nit'               => 'abc123abc',
      'address'           => 'Ciudad Test 2',
      'store_turn_id'     => $storeTurn->id,
      'items'             => [
        [
          'id'         => $presentationC->id,
          'quantity'   => 8,
          'type'       => 'PRESENTATION',
          'unit_price' => $presentationC->price,
        ],
      ]
    ];

    $attributes = [
      'store_id' => $storeTurn->store_id,
      'sells' => [$sellA, $sellB],
    ];

    $this->postJson(route('sells-offline.store'), $attributes)
      ->assertCreated();

    $this->assertDatabaseHas('sells', [
      'store_id'      => $storeTurn->store_id,
      'total'         => 13.25,
      'seller_id'     => $seller->id,
      'store_turn_id' => $storeTurn->id,
    ]);

    $this->assertDatabaseHas('sells', [
      'store_id'      => $storeTurn->store_id,
      'total'         => 160,
      'seller_id'     => $seller->id,
      'store_turn_id' => $storeTurn->id,
    ]);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_sell()
  {
    $user = $this->signInWithPermissionsTo(['sells.destroy']);

    $sell = factory(Sell::class)->create();
    $sellInvoice = factory(SellInvoice::class)->create(['sell_id' => $sell->id]);
    $sellPayment = factory(SellPayment::class)->create(['sell_id' => $sell->id]);

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $sell->store()->first()->company_id]);
      } else {
        $user->stores()->sync($sell->store_id);
      }
    }

    $this->deleteJson(route('sells.destroy', $sell->id))
      ->assertOk();

    $this->assertDatabaseMissing('sells', $sell->toArray());
    $this->assertDatabaseMissing('sell_invoices', $sellInvoice->toArray());
    $this->assertDatabaseMissing('sell_payments', $sellPayment->toArray());
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_not_store_a_sell_with_symbols_in_client_nit()
  {
    $user = $this->signInWithPermissionsTo(['sells.store']);

    $storeTurn = factory(StoreTurn::class)->create(['is_open' => true]);

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $storeTurn->store->company_id]);
      } else {
        $user->stores()->sync($storeTurn->store_id);
      }
    }

    $presentationA = factory(Presentation::class)->create(['price' => 5]);

    $attributes = [
      'store_id'          => $storeTurn->store_id,
      'payment_method_id' => factory(PaymentMethod::class)->create()->id,
      'name'              => 'test',
      'nit'               => '123*123',
      'address'           => 'Ciudad Test',
      'phone'             => '88888888',
      'email'             => 'test@test.com',
      'store_turn_id'     => $storeTurn->id,
      'items'             => [
        [
          'id'         => $presentationA->id,
          'quantity'   => 2,
          'type'       => 'PRESENTATION',
          'unit_price' => $presentationA->price,
        ],
      ]
    ];

    $this->postJson(route('sells.store'), $attributes)
      ->assertStatus(422)
      ->assertJsonValidationErrors('nit');   
  }
}