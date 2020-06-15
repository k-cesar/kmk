<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceListDetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'price_list_details';

    /**
     * Run the migrations.
     * @table price_list_details
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('list_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('initial_price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();

            $table->index(["product_id"], "{$this->tableName}_product_id");

            $table->index(["list_id"], "{$this->tableName}_list_id");

            $table->foreign('list_id')->references('id')->on('price_lists');

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
