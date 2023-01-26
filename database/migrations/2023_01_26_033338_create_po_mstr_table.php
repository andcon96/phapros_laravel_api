<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoMstrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('po_mstr', function (Blueprint $table) {
            $table->id();
            $table->string('po_domain',15);
            $table->string('po_nbr',15);
            $table->string('po_ship',15);
            $table->string('po_site',15)->nullable();
            $table->string('po_vend',15);
            $table->date('po_ord_date');
            $table->date('po_due_date');
            $table->string('po_curr',15)->nullable();
            $table->decimal('po_ppn',8,2)->nullable();
            $table->string('po_status',2)->nullable();
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
        Schema::dropIfExists('po_mstr');
    }
}
