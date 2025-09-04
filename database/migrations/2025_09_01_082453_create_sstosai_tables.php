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
        // Schema::create('sstosai18t', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('sai_a');
        //     $table->integer('month_a');
        //     $table->integer('sai_b');
        //     $table->integer('month_b');
        //     $table->integer('sai_c');
        //     $table->integer('month_c');
        //     $table->integer('sai_d');
        //     $table->integer('month_d');
        //     $table->timestamps();
        // });
        // Schema::create('sstosai17t', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('sai_a');
        //     $table->integer('month_a');
        //     $table->integer('sai_b');
        //     $table->integer('month_b');
        //     $table->integer('sai_c');
        //     $table->integer('month_c');
        //     $table->integer('sai_d');
        //     $table->integer('month_d');
        //     $table->timestamps();
        // });
        // Schema::create('sstosai16t', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('sai_a');
        //     $table->integer('month_a');
        //     $table->integer('sai_b');
        //     $table->integer('month_b');
        //     $table->integer('sai_c');
        //     $table->integer('month_c');
        //     $table->integer('sai_d');
        //     $table->integer('month_d');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sstosai18t');
        Schema::dropIfExists('sstosai17t');
        Schema::dropIfExists('sstosai16');
    }
};
