<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePodDetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pod_det', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pod_po_id')->nullable()->index();
            $table->foreign('pod_po_id')->references('id')->on('po_mstr');
            $table->string('pod_domain',15);
            $table->integer('pod_line');
            $table->string('pod_part',15);
            $table->decimal('pod_qty_ord',8,2);
            $table->decimal('pod_qty_rcvd',8,2)->default(0);
            $table->decimal('pod_pur_cost',20,2)->default(0);
            $table->string('pod_loc',15)->nullable();
            $table->string('pod_lot',15)->nullable();
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
        Schema::dropIfExists('pod_det');
    }
}
