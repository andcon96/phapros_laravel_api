<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAngkutanCatatanToRcptTransportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rcpt_transport', function (Blueprint $table) {
            $table->string('rcptt_angkutan_catatan')->after('rcptt_is_segregated_desc')->nullabel();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rcpt_transport', function (Blueprint $table) {
            $table->dropColumn('rcptt_angkutan_catatan');
        });
    }
}
