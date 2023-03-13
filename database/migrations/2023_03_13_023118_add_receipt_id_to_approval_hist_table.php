<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiptIdToApprovalHistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approval_hist', function (Blueprint $table) {
            $table->unsignedBigInteger('apphist_receipt_id')->nullable()->index();
            $table->foreign('apphist_receipt_id')->references('id')->on('rcpt_mstr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approval_hist', function (Blueprint $table) {
            $table->dropForeign(['apphist_receipt_id']);
            $table->dropColumn('apphist_receipt_id');
        });
    }
}
