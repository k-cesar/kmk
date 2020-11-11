<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDTEsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'dtes';

    /**
     * Run the migrations.
     * @table deposits
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sell_id');
            $table->boolean('is_cancellation');
            $table->text('xml');
            $table->boolean('signing_success')->nullable();
            $table->text('signing_response')->nullable();
            $table->boolean('certifier_success')->nullable();
            $table->text('certifier_response')->nullable();
            $table->string('uuid')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sell_id')->references('id')->on('sells');
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
