<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockMovementsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'stock_movements';

    /**
     * Run the migrations.
     * @table stock_movements
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('date');
            $table->string('decription')->nullable();
            $table->enum('origin_type', ['PURCHASE', 'SELL', 'TRANSFER','MANUAL_ADJUSTMENT'])->default('SELL');
            $table->unsignedBigInteger('origin_id');
            $table->enum('movement_type', ['INPUT', 'OUTPUT', 'ADJUSTMENT'])->default('ADJUSTMENT');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('store_id')->references('id')->on('stores');

            $table->foreign('user_id')->references('id')->on('users');
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
