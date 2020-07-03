<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreSkusTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'store_skus';

    /**
     * Run the migrations.
     * @table store_skus
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('presentation_sku_id');
            $table->tinyInteger('is_active');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['store_id', 'presentation_sku_id']);

            $table->foreign('store_id')->references('id')->on('stores');

            $table->foreign('presentation_sku_id')->references('id')->on('presentation_skus');
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
