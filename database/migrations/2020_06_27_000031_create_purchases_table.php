<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'purchases';

    /**
     * Run the migrations.
     * @table purchases
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('user_id');
            $table->text('comments')->nullable();
            $table->string('invoice', 100);
            $table->string('serial_number', 100)->nullable();
            $table->timestamp('date');
            $table->double('total');
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('provider_id')->references('id')->on('providers');

            $table->foreign('store_id')->references('id')->on('stores');

            $table->foreign('user_id')->references('id')->on('users');

            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
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
