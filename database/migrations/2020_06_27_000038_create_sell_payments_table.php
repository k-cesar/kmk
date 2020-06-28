<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellPaymentsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sell_payments';

    /**
     * Run the migrations.
     * @table sell_payments
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sell_id');
            $table->enum('payment_method', ['CASH', 'CREDIT', 'DEBIT']);
            $table->double('amount');
            $table->string('card_four_digits', 4);
            $table->string('authorization', 100);
            $table->enum('status', ['VERIFIED', 'UNVERIFIED']);
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
