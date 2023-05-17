<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuhuKelembapanToRcptTransportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rcpt_transport', function (Blueprint $table) {
            $table->string('rcptt_kelembapan')->after('rcptt_angkutan_catatan')->nullable();
            $table->string('rcptt_suhu')->after('rcptt_kelembapan')->nullable();
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
            $table->dropColumn('rcptt_kelembapan');
            $table->dropColumn('rcptt_suhu');
        });
    }
}
