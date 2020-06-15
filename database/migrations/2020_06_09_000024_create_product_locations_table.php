<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductLocationsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'product_locations';

    /**
     * Run the migrations.
     * @table product_locations
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('location_id');
            $table->decimal('sale_price', 10, 2)->nullable();

            $table->index(["location_id"], "{$this->tableName}_location_id");

            $table->foreign('product_id')->references('id')->on('products');

            $table->foreign('location_id')->references('id')->on('locations');
        
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
