<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockMovementsDetailTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'stock_movements_detail';

    /**
     * Run the migrations.
     * @table stock_movements_detail
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stock_movement_id');
            $table->unsignedBigInteger('stock_store_id');
            $table->unsignedBigInteger('product_id');
            $table->float('quantity');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('stock_store_id')->references('id')->on('stock_stores');

            $table->foreign('stock_movement_id')->references('id')->on('stock_movements');

            $table->foreign('product_id')->references('id')->on('products');
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
