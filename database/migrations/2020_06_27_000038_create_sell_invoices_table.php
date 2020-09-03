<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellInvoicesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sell_invoices';

    /**
     * Run the migrations.
     * @table sell_invoices
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->string('invoice', 100);
            $table->unsignedBigInteger('sell_id');
            $table->string('nit', 15);
            $table->string('name', 150);
            $table->timestamp('date');
            $table->double('total');
            $table->enum('concilation_status', ['RECONCILED', 'PENDING']);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'invoice']);

            $table->foreign('company_id')->references('id')->on('companies');

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
