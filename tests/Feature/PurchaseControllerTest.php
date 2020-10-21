<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use Illuminate\Support\Arr;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Product\Product;
use App\Http\Modules\Purchase\Purchase;
use App\Http\Modules\Stock\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'PurchaseSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_purchase_resources()
  {
    $this->getJson(route('purchases.index'))->assertUnauthorized();
    $this->postJson(route('purchases.store'))->assertUnauthorized();
    $this->putJson(route('purchases.update', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_purchase_resources()
  {
    $this->signIn();
    
    $randomPurchaseId = Purchase::all()->random()->id;

    $this->getJson(route('purchases.index'))->assertForbidden();
    $this->postJson(route('purchases.store'))->assertForbidden();
    $this->putJson(route('purchases.update', $randomPurchaseId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_purchases()
  {
    $this->signInWithPermissionsTo(['purchases.index']);

    $storeId = Purchase::all()->first()->store_id;

    $response = $this->getJson(route('purchases.index', ['store_id' => $storeId]))
      ->assertOk();
    
    foreach (Purchase::where('store_id', $storeId)->limit(10)->get() as $purchase) {
      $response->assertJsonFragment($purchase->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_purchase()
  {
    $this->signInWithPermissionsTo(['purchases.show']);

    $purchase = Purchase::with('store:id,name', 
      'user:id,name', 
      'provider:id,name', 
      'paymentMethod:id,name',
      'purchaseDetails.product:id,description')
      ->first();

    $this->getJson(route('purchases.show', $purchase->id))
      ->assertOk()
      ->assertJson($purchase->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_purchase()
  {
    $user = $this->signInWithPermissionsTo(['purchases.store']);

    $store = factory(Store::class)->create();
    $productA = factory(Product::class)->create();
    $productB = factory(Product::class)->create();

    $attributes = factory(Purchase::class)->raw([
      'store_id' => $store->id,
      'user_id'  => $user->id,
      'total'    => 26000,
    ]);

    $productsAttributes = [
      'products' => [
        [
          'id'         => $productA->id,
          'quantity'   => 10,
          'unit_price' => 100
        ],
        [
          'id'         => $productB->id,
          'quantity'   => 50,
          'unit_price' => 500
        ]
      ]
    ];

    $this->postJson(route('purchases.store'), array_merge($attributes, $productsAttributes))
      ->assertCreated();
    
    $this->assertDatabaseHas('purchases', Arr::except($attributes, ['date',]));

    $this->assertDatabaseHas('stock_movements', [
      'user_id'       => $user->id,
      'origin_id'     => $store->purchases->first()->id,
      'origin_type'   => StockMovement::OPTION_ORIGIN_TYPE_PURCHASE,
      'movement_type' => StockMovement::OPTION_MOVEMENT_TYPE_INPUT,
      'store_id'      => $store->id,
    ]);

    foreach ($productsAttributes['products'] as $product) {
      $this->assertDatabaseHas('purchase_details', [
        'purchase_id' => $store->purchases->first()->id,
        'product_id'  => $product['id'],
        'quantity'    => $product['quantity'],
        'unit_price'  => $product['unit_price'],
        'total'       => $product['quantity'] * $product['unit_price'],
      ]);

      $this->assertDatabaseHas('stock_stores', [
        'store_id'   => $store->id,
        'product_id' => $product['id'],
        'quantity'   => $product['quantity'],
      ]);

      $this->assertDatabaseHas('stock_movements_detail', [
        'stock_movement_id' => $store->stockMovements->first()->id,
        'stock_store_id'    => $store->products()->wherePivot('product_id', $product['id'])->first()->pivot->id,
        'product_id'        => $product['id'],
        'quantity'          => $product['quantity'],
      ]);

    }

    $store = Store::whereHas('products')->where('id', '!=', $store->id)->first();
    $product = $store->products()->first();
    $quantityInStockBeforePurchase = $product->pivot->quantity;

    $attributes = factory(Purchase::class)->raw([
      'store_id' => $store->id,
      'user_id'  => $user->id,
      'total'    => 4000,
    ]);

    $productsAttributes = [
      'products' => [
        [
          'id'         => $product->id,
          'quantity'   => 20,
          'unit_price' => 200
        ]
      ]
    ];

    $this->postJson(route('purchases.store'), array_merge($attributes, $productsAttributes))
      ->assertCreated();

    $this->assertDatabaseHas('purchases', Arr::except($attributes, ['date',]));

    $this->assertDatabaseHas('stock_movements', [
      'user_id'       => $user->id,
      'origin_id'     => $store->purchases->last()->id,
      'origin_type'   => StockMovement::OPTION_ORIGIN_TYPE_PURCHASE,
      'movement_type' => StockMovement::OPTION_MOVEMENT_TYPE_INPUT,
      'store_id'      => $store->id,
    ]);

    $product = $productsAttributes['products'][0];

    $this->assertDatabaseHas('purchase_details', [
      'purchase_id' => $store->purchases->last()->id,
      'product_id'  => $product['id'],
      'quantity'    => $product['quantity'],
      'unit_price'  => $product['unit_price'],
      'total'       => $product['quantity'] * $product['unit_price'],

    ]);

    $this->assertDatabaseHas('stock_stores', [
      'store_id'   => $store->id,
      'product_id' => $product['id'],
      'quantity'   => $product['quantity'] + $quantityInStockBeforePurchase,
    ]);

    $this->assertDatabaseHas('stock_movements_detail', [
      'stock_movement_id' => $store->stockMovements->last()->id,
      'stock_store_id'    => $store->products()->wherePivot('product_id', $product['id'])->first()->pivot->id,
      'product_id'        => $product['id'],
      'quantity'          => $product['quantity'],
    ]);

  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_purchase()
  {
    $this->signInWithPermissionsTo(['purchases.update']);

    $purchase = factory(Purchase::class)->create();

    $attributes = factory(Purchase::class)->raw();

    $attributes = Arr::except($attributes, ['user_id', 'date', 'total']);

    $this->putJson(route('purchases.update', $purchase->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('purchases', $attributes);
  }
}