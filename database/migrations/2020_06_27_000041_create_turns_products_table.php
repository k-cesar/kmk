<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTurnsProductsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'turns_products';

    /**
     * Run the migrations.
     * @table turns_products
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('turn_id');
            $table->unsignedBigInteger('presentation_id');
            $table->double('price');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['turn_id', 'presentation_id']);

            $table->foreign('turn_id')->references('id')->on('turns');
            $table->foreign('presentation_id')->references('id')->on('presentations');
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
