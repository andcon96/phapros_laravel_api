<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRcptChecklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rcpt_checklist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rcptc_rcpt_id')->nullable()->index();
            $table->foreign('rcptc_rcpt_id')->references('id')->on('rcpt_mstr');
            $table->string('rcptc_imr_nbr')->nullable();
            $table->string('rcptc_article_nbr')->nullable();
            $table->date('rcptc_imr_date')->nullable();
            $table->date('rcptc_arrival_date')->nullable();
            $table->date('rcptc_prod_date')->nullable();
            $table->date('rcptc_exp_date')->nullable();
            $table->string('rcptc_manufacturer')->nullable();
            $table->string('rcptc_country')->nullable();
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
        Schema::dropIfExists('rcpt_checklist');
    }
}
