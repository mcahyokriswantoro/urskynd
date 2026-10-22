<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Edukasi Kulit');
            $table->string('image')->nullable();
            $table->text('excerpt');
            $table->longText('content');
            $table->string('read_time')->default('4 menit');
            $table->string('author_name')->default('Tim Medis URSKYND');
            $table->string('author_role')->default('Skin Specialist');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['category', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
