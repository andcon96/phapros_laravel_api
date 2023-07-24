<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRcptcDoNbrToRcptChecklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rcpt_checklist', function (Blueprint $table) {
            $table->string('rcptc_do_nbr')->after('rcptc_imr_nbr')->nullable();
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
        Schema::table('rcpt_checklist', function (Blueprint $table) {
            //
            $table->dropColumn('rcptc_do_nbr');
        });
    }
}
