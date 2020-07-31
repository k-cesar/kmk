<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresentationCombosStoresTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'presentation_combos_stores_turns';

    /**
     * Run the migrations.
     * @table presentation_combos_stores_turns
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('presentation_combo_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('turn_id');
            $table->double('suggested_price');
            $table->timestamps();

            $table->foreign('presentation_combo_id')->references('id')->on('presentation_combos');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('turn_id')->references('id')->on('turns');
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
