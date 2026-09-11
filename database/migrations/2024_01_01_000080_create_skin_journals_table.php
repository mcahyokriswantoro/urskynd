<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skin_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('journal_date');
            $table->string('skin_condition')->nullable(); // good, moderate, bad
            $table->string('mood')->nullable(); // happy, neutral, stressed, tired
            $table->text('notes')->nullable();
            $table->string('photo')->nullable();
            $table->integer('skin_score')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'journal_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skin_journals');
    }
};
