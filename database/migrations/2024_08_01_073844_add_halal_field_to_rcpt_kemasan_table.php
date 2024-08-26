<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHalalFieldToRcptKemasanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rcpt_kemasan', function (Blueprint $table) {
            $table->tinyInteger('rcptk_has_logo_halal')->nullable()->after('rcptk_is_manufacturer_label')->default(0);
            $table->tinyInteger('rcptk_no_logo_halal')->nullable()->after('rcptk_has_logo_halal')->default(0);
            $table->tinyInteger('rcptk_not_regulated_logo_halal')->nullable()->after('rcptk_no_logo_halal')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rcpt_kemasan', function (Blueprint $table) {
            $table->dropColumn('rcptk_has_logo_halal');
            $table->dropColumn('rcptk_no_logo_halal');
            $table->dropColumn('rcptk_not_regulated_logo_halal');
        });
    }
}
