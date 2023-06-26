<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRcptdExpDateToRcptdDetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rcptd_det', function (Blueprint $table) {
            $table->string('rcptd_exp_date',25)->nullable()->after('rcptd_site');
            $table->string('rcptd_manu_date',25)->nullable()->after('rcptd_exp_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rcptd_det', function (Blueprint $table) {
            $table->dropColumn('rcptd_exp_date');
            $table->dropColumn('rcptd_manu_date');
        });
    }
}
