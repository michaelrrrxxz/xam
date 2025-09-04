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
         Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('enrolled_students')->onDelete('cascade');
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->integer('total_score')->default(0);
            $table->integer('verbal')->default(0);
            $table->integer('non_verbal')->default(0);
            $table->integer('verbal_reasoning')->default(0);
            $table->integer('verbal_comprehension')->default(0);
            $table->integer('quantitative_reasoning')->default(0);
            $table->integer('figural_reasoning')->default(0);
            $table->string('total_performance_category');
            $table->string('verbal_performance_category');
            $table->string('non_verbal_performance_category');
            $table->string('verbal_reasoning_category');
            $table->string('verbal_comprehension_category');
            $table->string('quantitative_reasoning_category');
            $table->string('figural_reasoning_category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
