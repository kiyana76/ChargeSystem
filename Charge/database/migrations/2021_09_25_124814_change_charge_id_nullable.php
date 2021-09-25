<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeChargeIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charge_logs', function (Blueprint $table) {
            $table->dropForeign('charge_logs_charge_id_foreign');
            $table->unsignedBigInteger('charge_id')->nullable()->change();
            $table->foreign('charge_id')->on('charges')->references('id')->onDelete('cascade');
            $table->dropColumn('status');
        });
        Schema::table('charge_logs', function (Blueprint $table) {
            $table->enum('status', ['store', 'fail', 'not_found', 'used']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('charge_logs', function (Blueprint $table) {
            $table->dropForeign('charge_logs_charge_id_foreign');
            $table->unsignedBigInteger('charge_id')->change();
            $table->foreign('charge_id')->on('charges')->references('id')->onDelete('cascade');
            $table->dropColumn('status');
        });

        Schema::table('charge_logs', function (Blueprint $table) {
            $table->enum('status', ['store', 'fail', 'not_found']);
        });
    }
}
