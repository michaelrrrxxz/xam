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
        Schema::create('trials', function (Blueprint $table) {
            $table->id();
            $table->string('format', 15);
            $table->string('test_type', 15);
            $table->string('question', 250);
            $table->string('answer', 100);
            $table->string('ch1', 100);
            $table->string('ch2', 100);
            $table->string('ch3', 100);
            $table->string('ch4', 100);
            $table->string('ch5', 100);
            $table->string('qtype', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trials');
    }
};
