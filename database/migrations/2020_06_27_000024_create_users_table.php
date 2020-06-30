<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'users';

    /**
     * Run the migrations.
     * @table users
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->enum('type', ['ADMIN_MASTER', 'ADMIN_ENTERPRISE', 'ADMIN_STORES', 'SELLER'])->default('SELLER');
            $table->string('email', 100)->nullable();
            $table->string('phone', 50);
            $table->unsignedBigInteger('company_id');
            $table->string('username', 100);
            $table->text('password');
            $table->text('token')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'phone']);

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
