<?php

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRstossTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rstoss', function (Blueprint $table) {
            $table->integer('id')->nullable();
            $table->integer('raw_score_t')->nullable();
            $table->integer('scaled_score_t')->nullable();
            $table->integer('raw_score_v')->nullable();
            $table->integer('scaled_score_v')->nullable();
            $table->integer('raw_score_nv')->nullable();
            $table->integer('scaled_score_nv')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rstoss');
    }
}