<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('li_laporan_id')->nullable()->index();
            $table->foreign('li_laporan_id')->references('id')->on('laporan_receipt');
            $table->string('li_path')->nullable();
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
        Schema::dropIfExists('laporan_images');
    }
}
