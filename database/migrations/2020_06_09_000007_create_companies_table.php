<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'companies';

    /**
     * Run the migrations.
     * @table companies
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nit', 50)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('comercial_name')->nullable();
            $table->string('comercial_address')->nullable();
            $table->enum('active', ['Y', 'N'])->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();

            $table->index(["currency_id"], "{$this->tableName}_currency_id");

            $table->foreign('currency_id')->references('id')->on('currencies');
        
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
