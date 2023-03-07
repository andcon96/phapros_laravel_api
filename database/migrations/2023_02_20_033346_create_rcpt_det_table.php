<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRcptDetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rcptd_det', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rcptd_rcpt_id')->nullable()->index();
            $table->foreign('rcptd_rcpt_id')->references('id')->on('rcpt_mstr');
            $table->biginteger('rcptd_line')->nullable();
            $table->string('rcptd_part')->nullable();
            $table->decimal('rcptd_qty_arr',8,2)->default(0);
            $table->decimal('rcptd_qty_appr',8,2)->default(0);
            $table->decimal('rcptd_qty_rej',8,2)->default(0);
            $table->string('rcptd_loc',15)->nullable();
            $table->string('rcptd_lot',15)->nullable();
            $table->string('rcptd_batch',15)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rcptd_det');
    }
}
