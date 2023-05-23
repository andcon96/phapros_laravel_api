<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForecastRencanaProduksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forecast_rencana_produksi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_rp_item')->index();
            $table->foreign('id_rp_item')->references('id')->on('item_m_r_p')->onDelete('restrict');
            $table->tinyInteger('isForecast')->default(0);
            $table->string('rp_mrp_nbr', 18);
            $table->string('rp_mrp_bulan', 5);
            $table->string('rp_mrp_tahun', 4);
            $table->string('rp_mrp_dataset', 20);
            $table->string('rp_mrp_type', 10);
            $table->date('rp_mrp_due_date');
            $table->decimal('rp_mrp_qty');
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
        Schema::dropIfExists('forecast_rencana_produksi');
    }
}
