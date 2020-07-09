<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'stores';

    /**
     * Run the migrations.
     * @table stores
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 150);
            $table->text('address');
            $table->double('petty_cash_amount');
            $table->unsignedBigInteger('store_type_id');
            $table->unsignedBigInteger('store_chain_id');
            $table->unsignedBigInteger('store_flag_id');
            $table->unsignedBigInteger('location_type_id');
            $table->unsignedBigInteger('store_format_id');
            $table->float('size');
            $table->unsignedBigInteger('socioeconomic_level_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('municipality_id');
            $table->unsignedBigInteger('zone_id');
            $table->double('latitute');
            $table->double('longitude');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['zone_id', 'name']);

            $table->foreign('store_type_id')->references('id')->on('store_types');

            $table->foreign('store_chain_id')->references('id')->on('store_chains');

            $table->foreign('store_flag_id')->references('id')->on('store_flags');

            $table->foreign('location_type_id')->references('id')->on('location_types');

            $table->foreign('store_format_id')->references('id')->on('store_formats');

            $table->foreign('socioeconomic_level_id')->references('id')->on('socioeconomic_levels');

            $table->foreign('state_id')->references('id')->on('states');

            $table->foreign('municipality_id')->references('id')->on('municipalities');

            $table->foreign('zone_id')->references('id')->on('zones');
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
