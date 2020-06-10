<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'currencies';

    /**
     * Run the migrations.
     * @table currencies
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('symbol', 10)->nullable();
            $table->string('description', 20)->nullable();
            $table->enum('active', ['Y', 'N'])->nullable();
            $table->unsignedBigInteger('company')->nullable();
            $table->enum('main_currency', ['Y', 'N'])->nullable();

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
