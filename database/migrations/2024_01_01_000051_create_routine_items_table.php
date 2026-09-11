<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routine_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_product_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('order_number')->default(0);
            $table->text('usage_instruction')->nullable();
            $table->timestamps();

            $table->index(['routine_id', 'order_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_items');
    }
};
