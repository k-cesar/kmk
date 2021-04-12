<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockCountsDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_counts_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stock_count_id');
            $table->unsignedBigInteger('product_id');
            $table->float('quantity');
            $table->float('quantity_stock');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('stock_count_id')->references('id')->on('stock_counts');
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
        Schema::dropIfExists('stock_counts_detail');
    }
}
