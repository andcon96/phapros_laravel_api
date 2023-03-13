<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanReceipt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_receipt', function (Blueprint $table) {
            $table->id();
            $table->string('laporan_rcptnbr',15)->nullable();
            $table->string('laporan_ponbr',15)->nullable();
            $table->string('laporan_part')->nullable();
            $table->date('laporan_tgl_masuk')->nullable();
            $table->decimal('laporan_jmlmasuk',8,2)->default(0)->nullable();
            $table->string('laporan_no')->nullable();
            $table->string('laporan_lot',15)->nullable();
            $table->date('laporan_tgl')->nullable();
            $table->string('laporan_supplier')->nullable();
            $table->text('laporan_komplain')->nullable();
            $table->text('laporan_keterangan')->nullable();
            $table->text('laporan_komplaindetail')->nullable();
            $table->string('laporan_angkutan')->nullable();
            $table->string('laporan_nopol')->nullable();
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
        Schema::dropIfExists('laporan_receipt');
    }
}
