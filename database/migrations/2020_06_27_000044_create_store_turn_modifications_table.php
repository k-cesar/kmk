<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreTurnModificationsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'store_turn_modifications';

    /**
     * Run the migrations.
     * @table store_turn_modifications
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('store_turn_id');
            $table->unsignedBigInteger('store_id');
            $table->double('amount');
            $table->enum('modification_type', ['CASH PURCHASE', 'DEPOSIT', 'OTHER']);
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('store_turn_id')->references('id')->on('store_turns');

            $table->foreign('store_id')->references('id')->on('stores');
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
