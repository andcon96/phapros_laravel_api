<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRcptMstrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rcpt_mstr', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rcpt_po_id')->nullable()->index();
            $table->foreign('rcpt_po_id')->references('id')->on('po_mstr');
            $table->string('rcpt_domain',15);
            $table->string('rcpt_nbr',15);
            $table->enum('rcpt_status',['created','finished'])->nullable();
            $table->unsignedBigInteger('rcpt_user_id')->nullable()->index();
            $table->foreign('rcpt_user_id')->references('id')->on('users');
            $table->date('rcpt_date')->nullable();
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
        Schema::dropIfExists('rcpt_mstr');
    }
}
