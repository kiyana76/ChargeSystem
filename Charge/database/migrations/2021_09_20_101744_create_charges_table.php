<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('charge_category_id');
            $table->foreign('charge_category_id')->on('charge_categories')->references('id')->onDelete('cascade');
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->uuid('code')->unique();
            $table->dateTime('expire_date');
            $table->enum('sold_status', ['sold', 'burnt', 'free']);
            $table->enum('status', ['valid', 'invalid', 'lock']);
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
        Schema::dropIfExists('charges');
    }
}
