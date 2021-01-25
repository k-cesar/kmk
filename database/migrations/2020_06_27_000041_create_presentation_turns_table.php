<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresentationTurnsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'presentations_turns';

    /**
     * Run the migrations.
     * @table presentations_turns
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('presentation_id');
            $table->unsignedBigInteger('turn_id');
            $table->double('price');
            $table->timestamps();

            $table->primary(['presentation_id', 'turn_id']);

            $table->foreign('presentation_id')->references('id')->on('presentations');
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
