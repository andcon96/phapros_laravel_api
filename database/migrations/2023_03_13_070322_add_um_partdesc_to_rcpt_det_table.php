<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUmPartdescToRcptDetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rcptd_det', function (Blueprint $table) {
            $table->string('rcptd_part_desc')->nullable();
            $table->string('rcptd_part_um')->nullable();
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
            $table->dropColumn('rcptd_part_desc');
            $table->dropColumn('rcptd_part_um');
            //
        });
    }
}
