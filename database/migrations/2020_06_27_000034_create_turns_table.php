<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTurnsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'turns';

    /**
     * Run the migrations.
     * @table turns
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('store_id');
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->tinyInteger('is_active');
            $table->tinyInteger('is_default');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['store_id', 'name']);
            $table->unique(['store_id', 'start_time', 'end_time']);

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
