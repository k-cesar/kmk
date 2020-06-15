<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'locations';

    /**
     * Run the migrations.
     * @table locations
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('name')->nullable();
            $table->enum('active', ['Y', 'N'])->nullable();
            $table->string('type', 20)->nullable();
            $table->unsignedBigInteger('municipalities_id')->nullable();

            $table->index(["company_id"], "{$this->tableName}_company_id");

            $table->foreign('company_id')->references('id')->on('companies');
        
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
