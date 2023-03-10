<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRcptTransportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rcpt_transport', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rcptt_rcpt_id')->nullable()->index();
            $table->foreign('rcptt_rcpt_id')->references('id')->on('rcpt_mstr');

            $table->string('rcptt_transporter_no')->nullable();
            $table->string('rcptt_police_no')->nullable();
            
            $table->boolean('rcptt_is_clean')->default(0);
            $table->string('rcptt_is_clean_desc')->nullable();
            $table->boolean('rcptt_is_dry')->default(0);
            $table->string('rcptt_is_dry_desc')->nullable();
            $table->boolean('rcptt_is_not_spilled')->default(0);
            $table->string('rcptt_is_not_spilled_desc')->nullable();
            
            $table->boolean('rcptt_is_position_single')->default(0);
            $table->string('rcptt_is_position_single_desc')->nullable();
            $table->boolean('rcptt_is_segregated')->default(0);
            $table->string('rcptt_is_segregated_desc')->nullable();

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
        Schema::dropIfExists('rcpt_transport');
    }
}
