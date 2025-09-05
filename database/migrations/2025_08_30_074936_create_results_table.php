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
            $table->string('test_ip');


            $table->integer('total_score')->default(0); //raw score
            $table->integer('scaled_score_t')->default(0);
            $table->integer('sai_t')->default(0);
            $table->integer('pba_pr_t')->default(0); //performance by age percentile rank total
            $table->integer('pba_s_t')->default(0); //performance by age stanine total
            $table->integer('pbg_pr_t')->default(0); //performance by grade percentile rank total
            $table->integer('pbg_s_t')->default(0); //performance by grade stanine total
//verbal
            $table->integer('verbal')->default(0); //verbal score
            $table->integer('scaled_score_v')->default(0);
            $table->integer('sai_v')->default(0);
            $table->integer('pba_pr_v')->default(0); //performance by age percentile rank total
            $table->integer('pba_s_v')->default(0); //performance by age stanine total
            $table->integer('pbg_pr_v')->default(0); //performance by grade percentile rank total
            $table->integer('pbg_s_v')->default(0); //performance by grade stanine total
//non verbal
            $table->integer('non_verbal')->default(0); //non verbak score
            $table->integer('scaled_score_nv')->default(0);
            $table->integer('sai_nv')->default(0);
            $table->integer('pba_pr_nv')->default(0); //performance by age percentile rank total
            $table->integer('pba_s_nv')->default(0); //performance by age stanine total
            $table->integer('pbg_pr_nv')->default(0); //performance by grade percentile rank total
            $table->integer('pbg_s_nv')->default(0); //performance by grade stanine total


            $table->integer('verbal_comprehension')->default(0);
            $table->string('verbal_comprehension_category');

            $table->integer('verbal_reasoning')->default(0);
            $table->string('verbal_reasoning_category');

            $table->integer('figural_reasoning')->default(0);
            $table->string('figural_reasoning_category');

            $table->integer('quantitative_reasoning')->default(0);
            $table->string('quantitative_reasoning_category');

            $table->string('total_performance_category');
            $table->string('verbal_performance_category');
            $table->string('non_verbal_performance_category');



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
