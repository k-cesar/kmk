<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'clients';

    /**
     * Run the migrations.
     * @table clients
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->enum('type', ['INDIVIDUAL', 'CORPORATION']);
            $table->unsignedBigInteger('country_id');
            $table->string('nit', 15);
            $table->string('uuid', 50)->unique();
            $table->text('address')->nullable();
            $table->enum('sex', ['MALE', 'FEMALE']);
            $table->text('biometric_id')->unique()->nullable();
            $table->date('birthdate');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['country_id', 'nit']);

            $table->foreign('country_id')->references('id')->on('countries');
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
