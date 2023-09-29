<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorQxtendApproveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error_qxtend_approve', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('eqa_rcpt_id')->nullable()->index();
            $table->foreign('eqa_rcpt_id')->references('id')->on('rcpt_mstr');
            
            $table->string('eqa_rcpt_nbr',15);
            $table->text('eqa_qxtend_message')->nullable();
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
        Schema::dropIfExists('error_qxtend_approve');
    }
}
