<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'products';

    /**
     * Run the migrations.
     * @table products
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 20)->nullable();
            $table->string('name', 100)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->enum('active', ['Y', 'N'])->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->string('barcode')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();

            $table->index(["subcategory_id"], "{$this->tableName}_subcategory_id");

            $table->index(["brand_id"], "{$this->tableName}_brand_id");

            $table->foreign('subcategory_id')->references('id')->on('subcategories');

            $table->foreign('brand_id')->references('id')->on('brands');
        
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
