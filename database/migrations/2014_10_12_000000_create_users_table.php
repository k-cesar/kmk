<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('username', 50)->nullable()->unique();;
            $table->string('email')->unique();
            $table->enum('active', ['Y', 'N'])->nullable();
            $table->string('user_type', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('token')->nullable();;
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
