<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skin_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('image_path')->nullable();
            $table->date('analysis_date');
            $table->integer('overall_score')->default(0); // 0-100
            $table->decimal('estimated_skin_age', 4, 1)->nullable();
            $table->foreignId('skin_type_id')->nullable()->constrained()->onDelete('set null');
            $table->text('summary')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'analysis_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skin_analyses');
    }
};
