<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyClientsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'company_clients';

    /**
     * Run the migrations.
     * @table company_clients
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('company_id');
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->timestamps();

            $table->primary(['company_id', 'client_id']);

            $table->foreign('client_id')->references('id')->on('clients');

            $table->foreign('company_id')->references('id')->on('companies');
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
