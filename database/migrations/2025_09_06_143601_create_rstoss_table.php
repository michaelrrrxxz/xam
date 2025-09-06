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
        Schema::create('rstoss', function (Blueprint $table) {
            $table->id(); // auto-increment primary key
            $table->integer('raw_score_t')->nullable();
            $table->integer('scaled_score_t')->nullable();
            $table->integer('raw_score_v')->nullable();
            $table->integer('scaled_score_v')->nullable();
            $table->integer('raw_score_nv')->nullable();
            $table->integer('scaled_score_nv')->nullable();
            $table->timestamps(); // optional, adds created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rstoss');
    }
};

