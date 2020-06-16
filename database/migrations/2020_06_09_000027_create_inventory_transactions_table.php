<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTransactionsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'inventory_transactions';

    /**
     * Run the migrations.
     * @table inventory_transactions
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('income_purchase_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->dateTime('date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('quantity')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();

            $table->index(["invoice_id"], "{$this->tableName}_invoice_id");

            $table->index(["user_id"], "{$this->tableName}_user_id");

            $table->index(["income_purchase_id"], "{$this->tableName}_income_purchase_id");

            $table->index(["product_id"], "{$this->tableName}_product_id");

            $table->foreign('income_purchase_id')->references('id')->on('income_purchase_details');

            $table->foreign('invoice_id')->references('id')->on('invoices');

            $table->foreign('product_id')->references('id')->on('products');

            $table->foreign('user_id')->references('id')->on('users');
        
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->tableName);
     }
}
