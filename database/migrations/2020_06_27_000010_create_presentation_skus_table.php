<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresentationSkusTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'presentation_skus';

    /**
     * Run the migrations.
     * @table presentation_skus
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 100)->unique();
            $table->string('description');
            $table->unsignedBigInteger('product_presentation_id');
            $table->tinyInteger('seasonal_product');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_presentation_id')->references('id')->on('product_presentations');
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
