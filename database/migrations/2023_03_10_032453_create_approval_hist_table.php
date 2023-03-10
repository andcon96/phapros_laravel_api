<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovalHistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_hist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apphist_user_id')->nullable()->index();
            $table->foreign('apphist_user_id')->references('id')->on('users');
            $table->string('apphist_po_domain');
            $table->string('apphist_po_nbr');
            $table->enum('apphist_status',['Approved','Rejected'])->nullable();
            $table->date('apphist_approved_date')->nullable();
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
        Schema::dropIfExists('approval_hist');
    }
}
