<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomePurchaseDetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'income_purchase_details';

    /**
     * Run the migrations.
     * @table income_purchase_details
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('income_purchase_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();

            $table->index(["income_purchase_id"], "{$this->tableName}_income_purchase_id");

            $table->index(["product_id"], "{$this->tableName}_product_id");

            $table->foreign('income_purchase_id')->references('id')->on('income_purchases');

            $table->foreign('product_id')->references('id')->on('products');
        
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
