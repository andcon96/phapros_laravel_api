<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUmKonvToRcptdDetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rcptd_det', function (Blueprint $table) {
            $table->decimal('rcptd_um_konv',10,2)->nullable()->after('rcptd_qty_per_package');
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
            $table->dropColumn('rcptd_um_konv');
        });
    }
}
