<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_concerns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skin_journal_id')->constrained()->onDelete('cascade');
            $table->foreignId('skin_concern_id')->constrained()->onDelete('cascade');
            $table->enum('severity', ['mild', 'moderate', 'severe'])->default('moderate');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_concerns');
    }
};
