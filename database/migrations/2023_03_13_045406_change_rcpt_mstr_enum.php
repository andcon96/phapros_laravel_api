<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeRcptMstrEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
            DB::statement("ALTER TABLE rcpt_mstr MODIFY rcpt_status ENUM('created','finished','rejected')");
        
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
            DB::statement("ALTER TABLE rcpt_mstr MODIFY rcpt_status ENUM('created','finished')");
        
        //
    }
}
