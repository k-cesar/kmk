<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreTurnsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'store_turns';

    /**
     * Run the migrations.
     * @table store_turns
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('turn_id');
            $table->tinyInteger('is_open');
            $table->timestamp('open_date');
            $table->double('open_petty_cash_amount');
            $table->unsignedBigInteger('open_by');
            $table->timestamp('close_date')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->double('closed_petty_cash_amount')->nullable();
            $table->double('expenses_in_not_purchases')->default(0);
            $table->string('expenses_reason')->nullable();
            $table->double('card_sales')->default(0);
            $table->double('cash_on_hand')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('store_id')->references('id')->on('stores');

            $table->foreign('turn_id')->references('id')->on('turns');

            $table->foreign('open_by')->references('id')->on('users');

            $table->foreign('closed_by')->references('id')->on('users');
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
