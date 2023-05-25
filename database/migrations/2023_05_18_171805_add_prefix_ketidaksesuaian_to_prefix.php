<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrefixKetidaksesuaianToPrefix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prefix', function (Blueprint $table) {
            
            $table->string('prefix_ketidaksesuaian')->after('prefix_rcpt_rn')->default('RCT')->nullable();
            $table->string('prefix_ketidaksesuaian_rn')->after('prefix_ketidaksesuaian')->default('000000')->nullable();
            
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prefix', function (Blueprint $table) {
            $table->dropColumn('prefix_ketidaksesuaian');
            $table->dropColumn('prefix_ketidaksesuaian_rn');

            //
        });
    }
}
