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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_affiliate')->default(false)->after('role');
            $table->string('affiliate_code', 40)->nullable()->unique()->after('is_affiliate');
            $table->string('affiliate_status', 20)->default('active')->after('affiliate_code');
            $table->decimal('commission_rate', 5, 2)->default(10.00)->after('affiliate_status');
            $table->bigInteger('commission_balance')->default(0)->after('commission_rate');
            $table->bigInteger('total_commission_earned')->default(0)->after('commission_balance');
            $table->string('bank_name', 60)->nullable()->after('total_commission_earned');
            $table->string('bank_account_number', 60)->nullable()->after('bank_name');
            $table->string('bank_account_holder', 120)->nullable()->after('bank_account_number');
        });

        Schema::create('affiliate_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->cascadeOnDelete();
            $table->string('order_code', 40)->unique();
            $table->string('customer_name');
            $table->string('customer_phone', 30);
            $table->string('package_name');
            $table->bigInteger('sale_amount')->default(0);
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            $table->bigInteger('commission_amount')->default(0);
            $table->string('payment_method', 100)->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pending'); // pending, approved, rejected
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->cascadeOnDelete();
            $table->string('withdrawal_code', 40)->unique();
            $table->bigInteger('amount');
            $table->string('bank_name', 60);
            $table->string('bank_account_number', 60);
            $table->string('bank_account_holder', 120);
            $table->string('status', 20)->default('pending'); // pending, approved, rejected
            $table->text('admin_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_withdrawals');
        Schema::dropIfExists('affiliate_sales');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_affiliate',
                'affiliate_code',
                'affiliate_status',
                'commission_rate',
                'commission_balance',
                'total_commission_earned',
                'bank_name',
                'bank_account_number',
                'bank_account_holder',
            ]);
        });
    }
};

