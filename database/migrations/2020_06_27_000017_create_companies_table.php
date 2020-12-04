<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('name');
            $table->text('reason');
            $table->string('regime')->nullable();
            $table->string('nit', 15);
            $table->string('phone', 50);
            $table->string('address');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('currency_id');
            $table->tinyInteger('allow_add_products')->default(0);
            $table->tinyInteger('allow_add_stores')->default(0);
            $table->tinyInteger('is_electronic_invoice')->default(0);
            $table->tinyInteger('uses_fel')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['country_id', 'nit']);

            $table->foreign('country_id')->references('id')->on('countries');

            $table->foreign('currency_id')->references('id')->on('currencies');
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
