<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusApproveToRcptMstrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rcpt_mstr', function (Blueprint $table) {
            $table->enum('rcpt_approve_status',[0,1])->nullable();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rcpt_mstr', function (Blueprint $table) {
            $table->dropColumn('rcpt_approve_status');
            //
        });
    }
}
