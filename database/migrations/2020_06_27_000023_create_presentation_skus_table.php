<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresentationSkusTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'presentation_skus';

    /**
     * Run the migrations.
     * @table presentation_skus
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('presentation_id');
            $table->string('code', 100);
            $table->string('description');
            $table->tinyInteger('seasonal_product');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);
            
            $table->foreign('presentation_id')->references('id')->on('presentations');
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
