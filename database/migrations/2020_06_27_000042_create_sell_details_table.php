<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellDetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sell_details';

    /**
     * Run the migrations.
     * @table sell_details
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->integer('item_line');
            $table->unsignedBigInteger('sell_id');
            $table->unsignedBigInteger('presentation_id');
            $table->string('description');
            $table->double('price');
            $table->float('quantity');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['item_line', 'sell_id']);

            $table->foreign('sell_id')->references('id')->on('sells');

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
