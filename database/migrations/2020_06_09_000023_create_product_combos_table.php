<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCombosTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'product_combos';

    /**
     * Run the migrations.
     * @table product_combos
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('main_product_id');
            $table->unsignedBigInteger('related_product_id');

            $table->index(["related_product_id"], "{$this->tableName}_related_product_id");

            $table->foreign('main_product_id')->references('id')->on('products');

            $table->foreign('related_product_id')->references('id')->on('products');
        
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
