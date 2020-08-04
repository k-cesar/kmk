<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreFlagsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'store_flags';

    /**
     * Run the migrations.
     * @table store_flags
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 150);
            $table->unsignedBigInteger('store_chain_id');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['store_chain_id', 'name']);

            $table->foreign('store_chain_id')->references('id')->on('store_chains');
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
