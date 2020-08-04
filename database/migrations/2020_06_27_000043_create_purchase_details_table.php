<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseDetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'purchase_details';

    /**
     * Run the migrations.
     * @table purchase_details
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_id');
            $table->integer('item_line');
            $table->unsignedBigInteger('presentation_id');
            $table->float('quantity');
            $table->double('unit_price');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['purchase_id', 'presentation_id']);

            $table->foreign('presentation_id')->references('id')->on('presentations');

            $table->foreign('purchase_id')->references('id')->on('purchases');
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
