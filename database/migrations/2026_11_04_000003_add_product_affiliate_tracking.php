<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(10.00)->after('price');
        });

        Schema::table('affiliate_sales', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('affiliate_id')->constrained('products')->nullOnDelete();
            $table->integer('quantity')->default(1)->after('package_name');
        });

        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('target_path', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_clicks');

        Schema::table('affiliate_sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn('quantity');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });
    }
};

