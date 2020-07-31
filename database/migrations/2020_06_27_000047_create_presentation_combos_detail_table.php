<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresentationCombosDetailTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'presentation_combos_detail';

    /**
     * Run the migrations.
     * @table presentation_combos_detail
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('presentation_combo_id');
            $table->unsignedBigInteger('product_presentation_id');

            $table->primary(['presentation_combo_id', 'product_presentation_id']);

            $table->foreign('presentation_combo_id')->references('id')->on('presentation_combos');
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
