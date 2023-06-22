<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKetersediaanTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ketersediaan_temp', function (Blueprint $table) {
                $table->string('t_mrp_part');
                $table->integer('t_mrp_duedate');
                $table->integer('t_year_duedate');
                $table->string('t_pt_desc1');
                $table->string('t_pt_desc2');
                $table->string('t_pt_um');
                $table->decimal('t_ld_qty_oh')->nullable();
                $table->decimal('t_pod_qty_oh')->nullable();
                $table->decimal('t_rqm_qty')->nullable();
                $table->decimal('t_qty_bahan')->nullable();
                $table->decimal('t_qty_stok')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ketersediaan_temp');
    }
}
