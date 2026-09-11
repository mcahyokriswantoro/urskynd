<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_skin_concerns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('skin_concern_id')->constrained()->onDelete('cascade');
            $table->enum('severity', ['mild', 'moderate', 'severe'])->default('moderate');
            $table->timestamps();

            $table->unique(['user_id', 'skin_concern_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_skin_concerns');
    }
};
