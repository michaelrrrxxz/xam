<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::create('scaledscores', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('raw_score_t');
        //     $table->integer('scaled_score_t');
        //     $table->integer('raw_score_v');
        //     $table->integer('scaled_score_v');
        //     $table->integer('raw_score_nv');
        //     $table->integer('scaled_score_nv');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scaledscores');
    }
};
