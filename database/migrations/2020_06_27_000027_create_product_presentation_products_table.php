<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPresentationProductsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'product_presentation_products';

    /**
     * Run the migrations.
     * @table product_presentation_products
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->tinyInteger('is_minimal_expression');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_presentation_id');
            $table->float('units');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['product_id', 'product_presentation_id']);

            $table->foreign('product_presentation_id')->references('id')->on('product_presentations');

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
