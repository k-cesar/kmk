<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsAccessTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'locations_access';

    /**
     * Run the migrations.
     * @table locations_access
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('user_id');

            $table->index(["user_id"], "{$this->tableName}_user_id");

            $table->foreign('location_id')->references('id')->on('locations');

            $table->foreign('user_id')->references('id')->on('users');
        
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
