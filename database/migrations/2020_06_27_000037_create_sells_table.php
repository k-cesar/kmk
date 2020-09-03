<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sells';

    /**
     * Run the migrations.
     * @table sells
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('store_turn_id');
            $table->unsignedBigInteger('client_id');
            $table->string('description')->nullable();
            $table->timestamp('date');
            $table->double('total');
            $table->unsignedBigInteger('seller_id');
            $table->enum('status', ['PENDING', 'CANCELLED', 'PAID']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients');

            $table->foreign('seller_id')->references('id')->on('users');

            $table->foreign('store_id')->references('id')->on('stores');

            $table->foreign('store_turn_id')->references('id')->on('store_turns');
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
