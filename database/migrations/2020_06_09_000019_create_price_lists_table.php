<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceListsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'price_lists';

    /**
     * Run the migrations.
     * @table price_lists
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('description', 100)->nullable();
            $table->enum('active', ['Y', 'N'])->nullable();

            $table->index(["location_id"], "{$this->tableName}_location_id");

            $table->foreign('location_id')->references('id')->on('locations');
        
            $table->timestamps();
            $table->softDeletes();
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
