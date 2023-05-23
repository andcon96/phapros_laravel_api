<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemMRPTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_m_r_p', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 28);
            $table->string('item_design_group', 8);
            $table->string('item_description', 48);
            // $table->string('item_pilar');
            // $table->string('item_hjp');
            $table->string('item_pareto', 5);
            $table->string('item_make_to_type', 3);
            $table->decimal('item_ed')->default(0);
            $table->decimal('item_bv')->default(0);
            $table->date('item_last_stock_date')->nullable();
            $table->decimal('item_stock_qty_oh');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_m_r_p');
    }
}
