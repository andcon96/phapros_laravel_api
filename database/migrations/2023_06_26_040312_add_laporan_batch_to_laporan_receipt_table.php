<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLaporanBatchToLaporanReceiptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laporan_receipt', function (Blueprint $table) {
            //
            $table->string('laporan_batch')->after('laporan_lot')->nullable();
            $table->string('laporan_imr')->after('laporan_batch')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('laporan_receipt', function (Blueprint $table) {
            //
            $table->dropColumn('laporan_batch');
            $table->dropColumn('laporan_imr');
        });
    }
}
