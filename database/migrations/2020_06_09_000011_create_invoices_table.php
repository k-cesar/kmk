<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'invoices';

    /**
     * Run the migrations.
     * @table invoices
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date')->nullable();
            $table->string('invoice_no', 20)->nullable();
            $table->string('invoice_serie', 10)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->enum('active', ['Y', 'N'])->nullable();
            $table->string('customers_name', 100)->nullable();
            $table->string('customers_address')->nullable();
            $table->string('customers_nit', 20)->nullable();
            $table->decimal('amount', 10, 2)->nullable();

            $table->index(["seller_id"], "{$this->tableName}_seller_id");

            $table->index(["customer_id"], "{$this->tableName}_customer_id");

            $table->index(["company_id"], "{$this->tableName}_company_id");

            $table->foreign('customer_id')->references('id')->on('customers');

            $table->foreign('seller_id')->references('id')->on('users');

            $table->foreign('company_id')->references('id')->on('companies');
        
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
