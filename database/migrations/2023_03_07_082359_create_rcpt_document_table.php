<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRcptDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rcpt_document', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rcptdoc_rcpt_id')->nullable()->index();
            $table->foreign('rcptdoc_rcpt_id')->references('id')->on('rcpt_mstr');

            $table->boolean('rcptdoc_is_certofanalys')->default(0);
            $table->string('rcptdoc_certofanalys')->nullable();
            $table->boolean('rcptdoc_is_msds')->default(0);
            $table->string('rcptdoc_msds')->nullable();
            $table->boolean('rcptdoc_is_forwarderdo')->default(0);
            $table->string('rcptdoc_forwarderdo')->nullable();
            $table->boolean('rcptdoc_is_packinglist')->default(0);
            $table->string('rcptdoc_packinglist')->nullable();
            $table->boolean('rcptdoc_is_otherdocs')->default(0);
            $table->string('rcptdoc_otherdocs')->nullable();

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
        Schema::dropIfExists('rcpt_document');
    }
}
