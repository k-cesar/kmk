<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomePurchasesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'income_purchases';

    /**
     * Run the migrations.
     * @table income_purchases
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('description')->nullable();
            $table->string('invoice_no', 20)->nullable();
            $table->string('invoice_serie', 20)->nullable();
            $table->dateTime('purchase_date')->nullable();
            $table->enum('type', ['purchase', 'adjustment', 'other'])->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();

            $table->index(["location_id"], "{$this->tableName}_location_id");

            $table->index(["supplier_id"], "{$this->tableName}_supplier_id");

            $table->index(["user_id"], "{$this->tableName}_user_id");

            $table->index(["payment_method_id"], "{$this->tableName}_payment_method_id");

            $table->foreign('user_id')->references('id')->on('users');

            $table->foreign('supplier_id')->references('id')->on('customers');

            $table->foreign('location_id')->references('id')->on('locations');

            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        
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
