<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblSstosai18nvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sstosai_18nv', function (Blueprint $table) {
            $table->integer('id')->nullable();
            $table->integer('sai_a')->nullable();
            $table->integer('month_a')->nullable();
            $table->integer('sai_b')->nullable();
            $table->integer('month_b')->nullable();
            $table->integer('sai_c')->nullable();
            $table->integer('month_c')->nullable();
            $table->integer('sai_d')->nullable();
            $table->integer('month_d')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_sstosai_18nv');
    }
}
