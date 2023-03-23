<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeTypeToRcptMstr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rcpt_mstr', function (Blueprint $table) {
            $table->string('rcpt_user_id')->nullable()->change();        
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
            $table->unsignedBigInteger('rcpt_user_id')->nullable()->index()->change();
        });
    }
}
