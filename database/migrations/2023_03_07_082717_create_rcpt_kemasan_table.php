<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRcptKemasanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rcpt_kemasan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rcptk_rcpt_id')->nullable()->index();
            $table->foreign('rcptk_rcpt_id')->references('id')->on('rcpt_mstr');

            $table->boolean('rcptk_kemasan_sacdos')->default(0);
            $table->string('rcptk_kemasan_sacdos_desc')->nullable();
            $table->boolean('rcptk_kemasan_drumvat')->default(0);
            $table->string('rcptk_kemasan_drumvat_desc')->nullable();
            $table->boolean('rcptk_kemasan_palletpeti')->default(0);
            $table->string('rcptk_kemasan_palletpeti_desc')->nullable();

            $table->tinyInteger('rcptk_is_clean')->default(0);
            $table->string('rcptk_is_clean_desc')->nullable();
            $table->tinyInteger('rcptk_is_dry')->default(0);
            $table->string('rcptk_is_dry_desc')->nullable();
            $table->tinyInteger('rcptk_is_not_spilled')->default(0);
            $table->string('rcptk_is_not_spilled_desc')->nullable();

            $table->tinyInteger('rcptk_is_sealed')->default(0);
            $table->tinyInteger('rcptk_is_manufacturer_label')->default(0);

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
        Schema::dropIfExists('rcpt_kemasan');
    }
}
