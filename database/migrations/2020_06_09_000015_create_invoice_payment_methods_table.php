<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicePaymentMethodsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'invoice_payment_methods';

    /**
     * Run the migrations.
     * @table invoice_payment_methods
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->decimal('amount', 10, 0)->nullable();

            $table->index(["payment_method_id"], "{$this->tableName}_payment_method_id");

            $table->foreign('invoice_id')->references('id')->on('invoices');

            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        
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
