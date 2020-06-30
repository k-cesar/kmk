<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesSocioeconomicTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'countries_socioeconomic';

    /**
     * Run the migrations.
     * @table countries_socioeconomic
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('socioeconomic_id');
            $table->unsignedBigInteger('country_id');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['socioeconomic_id', 'country_id']);

            $table->foreign('country_id')->references('id')->on('countries');

            $table->foreign('socioeconomic_id')->references('id')->on('socioeconomic_level');
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
