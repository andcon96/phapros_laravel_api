<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorNameToPoMstrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_mstr', function (Blueprint $table) {
            $table->string('po_vend_desc',255)->after('po_vend')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_mstr', function (Blueprint $table) {
            $table->dropColumn('po_vend_desc');
        });
    }
}
