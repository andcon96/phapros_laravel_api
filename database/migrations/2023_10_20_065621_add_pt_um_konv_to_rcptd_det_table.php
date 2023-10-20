<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPtUmKonvToRcptdDetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rcptd_det', function (Blueprint $table) {
            $table->string('rcptd_um_pr')->nullable();
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
            $table->dropColumn('rcptd_um_pr');
        });
    }
}
