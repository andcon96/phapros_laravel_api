<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemDescToPodDetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pod_det', function (Blueprint $table) {
            $table->string('pod_desc')->nullable()->after('pod_part');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pod_det', function (Blueprint $table) {
            $table->dropColumn('pod_desc');
        });
    }
}
