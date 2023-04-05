<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRcptFileUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rcpt_file_upload', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rcptfu_rcpt_id')->nullable()->index();
            $table->foreign('rcptfu_rcpt_id')->references('id')->on('rcpt_mstr');
            $table->string('rcptfu_path')->nullable();
            $table->tinyInteger('rcptfu_is_ttd')->default(0);
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
        Schema::dropIfExists('rcpt_file_upload');
    }
}
