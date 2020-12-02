<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('description')->unique();
            $table->tinyInteger('is_all_countries')->default(1);
            $table->integer('brand_id');
            $table->unsignedBigInteger('product_category_id');
            $table->unsignedBigInteger('product_subcategory_id');
            $table->tinyInteger('is_taxable');
            $table->tinyInteger('is_inventoriable');
            $table->unsignedBigInteger('uom_id');
            $table->double('suggested_price');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('brand_id')->references('id')->on('brands');

            $table->foreign('product_category_id')->references('id')->on('product_categories');

            $table->foreign('product_subcategory_id')->references('id')->on('product_subcategories');

            $table->foreign('uom_id')->references('id')->on('uoms');
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
