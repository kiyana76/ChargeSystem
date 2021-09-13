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
            $table->enum('type', ['admin', 'seller']);
            $table->string('name');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->on('companies')->references('id')->onDelete('cascade');
            $table->string('mobile')->unique()->notNullable();
            $table->string('email')->nullable();
            $table->string('password');
            $table->enum('status', ['active', 'inactive']);
            $table->timestamps();
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
