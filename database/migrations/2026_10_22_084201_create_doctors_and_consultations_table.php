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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->string('experience')->default('5+ Tahun Pengalaman');
            $table->string('hospital')->default('Klinik Mitra URSKYND');
            $table->string('rating')->default('4.9');
            $table->integer('reviews_count')->default(100);
            $table->text('specialties')->nullable();
            $table->boolean('available')->default(true);
            $table->string('price')->default('Gratis via URSKYND Support');
            $table->string('avatar')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->timestamps();
        });

        Schema::create('consultation_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('concern');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, contacted, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_bookings');
        Schema::dropIfExists('doctors');
    }
};
