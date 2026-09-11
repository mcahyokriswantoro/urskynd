<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skin_analysis_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skin_analysis_id')->constrained()->onDelete('cascade');
            $table->string('metric_type'); // hydration, pores, acne, pigmentation, wrinkles, redness, texture, sensitivity
            $table->integer('score')->default(0); // 0-100
            $table->enum('severity', ['excellent', 'good', 'moderate', 'poor', 'critical'])->default('moderate');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['skin_analysis_id', 'metric_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skin_analysis_metrics');
    }
};
